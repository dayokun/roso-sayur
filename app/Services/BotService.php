<?php

namespace App\Services;

use App\Models\BotSession;
use App\Models\Konsumen;
use App\Models\Pesanan;
use App\Models\PesananDetail;
use App\Models\Produk;
use Carbon\Carbon;

/**
 * State machine pemesanan via WhatsApp (PRD 6.1).
 * Alur: nama -> produk -> qty -> tambah? -> tgl_ambil -> konfirmasi.
 */
class BotService
{
    /**
     * Batas maksimal tanggal pengambilan: 30 hari dari hari ini.
     */
    private const MAX_TGL_AMBIL_HARI = 30;

    public function __construct(protected FonnteService $fonnte = new FonnteService()) {}

    /**
     * Proses satu pesan masuk, kembalikan teks balasan.
     */
    public function handle(string $noHp, string $pesan): string
    {
        $noHp = $this->fonnte->normalisasi($noHp);
        $session = BotSession::firstOrCreate(
            ['no_hp' => $noHp],
            ['state' => BotSession::STATE_IDLE, 'last_activity_at' => now()]
        );

        // Timeout sesi 15 menit (PRD 6.1.2)
        if ($session->state !== BotSession::STATE_IDLE && $session->sudahTimeout()) {
            $session->reset();

            return "Sesi pemesanan Anda telah berakhir. Ketik 'pesan' untuk memulai ulang.";
        }

        $text = trim($pesan);

        if (strtolower($text) === 'batal') {
            $session->reset();

            return 'Pesanan dibatalkan. Ketik *pesan* untuk memulai lagi.';
        }

        return match ($session->state) {
            BotSession::STATE_IDLE => $this->mulai($session, $text),
            BotSession::STATE_NAMA => $this->isiNama($session, $text),
            BotSession::STATE_PRODUK => $this->pilihProduk($session, $text),
            BotSession::STATE_QTY => $this->isiQty($session, $text),
            BotSession::STATE_TAMBAH => $this->tambahLagi($session, $text),
            BotSession::STATE_TGL_AMBIL => $this->isiTanggal($session, $text),
            BotSession::STATE_KONFIRMASI => $this->konfirmasi($session, $text),
            default => $this->fallback($session),
        };
    }

    // ---------- langkah-langkah ----------

    protected function mulai(BotSession $session, string $text): string
    {
        $konsumen = Konsumen::where('no_hp', $session->no_hp)->first();

        // Mode pesan cepat: "kol 3kg, sawi 2kg" — tanpa ketik "pesan" dulu
        $cepat = $this->parseCepat($text);
        if ($cepat !== null) {
            if ($konsumen) {
                $session->sentuh(BotSession::STATE_PRODUK, ['konsumen_id' => $konsumen->id]);

                return "Halo kembali, {$konsumen->nama}!\n" . $this->tambahCepat($session, $cepat);
            }
            // Konsumen baru: simpan dulu, minta nama, lalu proses otomatis
            $session->sentuh(BotSession::STATE_NAMA, ['pending_cepat' => $cepat]);

            return "Selamat datang di *Roso Sayur*! Siapa nama Anda?";
        }

        if (! preg_match('/pesan/i', $text)) {
            return "Halo, selamat datang di *Roso Sayur*!\nKetik *pesan* untuk mulai memesan sayur & buah segar.\n\nTips pesan cepat: ketik langsung, mis. *kol 3kg, sawi 2kg*";
        }

        if ($konsumen) {
            $session->sentuh(BotSession::STATE_PRODUK, ['konsumen_id' => $konsumen->id]);

            return "Halo kembali, {$konsumen->nama}!\n" . $this->daftarProduk();
        }

        $session->sentuh(BotSession::STATE_NAMA);

        return "Selamat datang di *Roso Sayur*! Siapa nama Anda?";
    }

    protected function isiNama(BotSession $session, string $text): string
    {
        if (mb_strlen($text) < 2) {
            return $this->gagal($session, 'Nama terlalu pendek. Siapa nama Anda?');
        }

        // Auto-register konsumen dari nama + no HP (PRD OI-04)
        $konsumen = Konsumen::where('no_hp', $session->no_hp)->first();
        if (! $konsumen) {
            $konsumen = Konsumen::create(['nama' => $text, 'no_hp' => $session->no_hp]);
        }
        $session->update(['nama' => $konsumen->nama]);

        // Ada pesanan cepat yang menunggu? Langsung proses setelah registrasi
        $pending = ($session->data ?? [])['pending_cepat'] ?? null;
        $session->sentuh(BotSession::STATE_PRODUK, ['konsumen_id' => $konsumen->id, 'pending_cepat' => null]);

        if (is_array($pending) && count($pending) > 0) {
            return "Halo {$konsumen->nama}!\n" . $this->tambahCepat($session, $pending);
        }

        return "Halo {$konsumen->nama}!\n" . $this->daftarProduk();
    }

    protected function pilihProduk(BotSession $session, string $text): string
    {
        // Mode pesan cepat: "kol 3kg" atau "kol 3kg, sawi 2kg"
        $cepat = $this->parseCepat($text);
        if ($cepat !== null) {
            return $this->tambahCepat($session, $cepat);
        }

        $produk = $this->cariProduk($text);
        if (! $produk) {
            return $this->gagal($session, "Produk tidak ditemukan. Pilih dari daftar:\n" . $this->daftarProduk());
        }

        $session->sentuh(BotSession::STATE_QTY, ['produk_id' => $produk->id]);

        return "Berapa *{$produk->satuan}* {$produk->nama} yang dipesan? (ketik angka saja)";
    }

    protected function isiQty(BotSession $session, string $text): string
    {
        $qty = str_replace(',', '.', trim($text));
        if (! is_numeric($qty) || (float) $qty <= 0) {
            return $this->gagal($session, 'Maaf, input tidak valid. Masukkan angka untuk jumlah.');
        }

        $produk = Produk::find($session->produkAktifId());
        if (! $produk) {
            $session->sentuh(BotSession::STATE_PRODUK);

            return $this->daftarProduk();
        }

        $keranjang = $session->keranjang();
        $keranjang[] = [
            'produk_id' => $produk->id,
            'nama' => $produk->nama,
            'satuan' => $produk->satuan,
            'qty' => (float) $qty,
        ];
        $session->sentuh(BotSession::STATE_TAMBAH, ['keranjang' => $keranjang, 'produk_id' => null]);

        return "Ditambahkan: {$produk->nama} {$qty} {$produk->satuan}.\nIngin tambah produk lain? (Ya/Tidak)";
    }

    protected function tambahLagi(BotSession $session, string $text): string
    {
        if (preg_match('/^ya\b/i', $text)) {
            $session->sentuh(BotSession::STATE_PRODUK);

            return $this->daftarProduk();
        }
        if (preg_match('/^tidak/i', $text)) {
            $session->sentuh(BotSession::STATE_TGL_AMBIL);

            return 'Kapan tanggal pengambilan? (format: YYYY-MM-DD, contoh: ' . Carbon::tomorrow()->toDateString() . ')';
        }

        return $this->gagal($session, 'Jawab *Ya* atau *Tidak*. Ingin tambah produk lain?');
    }

    protected function isiTanggal(BotSession $session, string $text): string
    {
        try {
            $tgl = Carbon::parse(trim($text))->toDateString();
        } catch (\Throwable) {
            return $this->gagal($session, 'Format tanggal salah. Gunakan YYYY-MM-DD, contoh: ' . Carbon::tomorrow()->toDateString());
        }

        if ($tgl < Carbon::today()->toDateString()) {
            return $this->gagal($session, 'Maaf, tanggal pengambilan tidak tersedia (sudah lewat). Pilih tanggal lain.');
        }

        $maks = Carbon::today()->addDays(self::MAX_TGL_AMBIL_HARI)->toDateString();
        if ($tgl > $maks) {
            return $this->gagal($session, "Maaf, tanggal pengambilan maksimal " . self::MAX_TGL_AMBIL_HARI . " hari dari hari ini ({$maks}). Pilih tanggal lain.");
        }

        $session->sentuh(BotSession::STATE_KONFIRMASI, ['tgl_ambil' => $tgl]);

        return "Konfirmasi pesanan Anda:\n" . $this->ringkasan($session)
            . "\nKetik *YA* untuk konfirmasi, atau *BATAL* untuk membatalkan.";
    }

    protected function konfirmasi(BotSession $session, string $text): string
    {
        if (! preg_match('/^ya$/i', trim($text))) {
            return $this->gagal($session, 'Ketik *YA* untuk konfirmasi atau *BATAL* untuk membatalkan.');
        }

        return $this->simpanPesanan($session);
    }

    // ---------- penyimpanan ----------

    protected function simpanPesanan(BotSession $session): string
    {
        $data = $session->data ?? [];
        $keranjang = $session->keranjang();
        $tglAmbil = $data['tgl_ambil'] ?? Carbon::tomorrow()->toDateString();

        if (empty($keranjang)) {
            $session->reset();

            return 'Keranjang kosong. Ketik *pesan* untuk mulai lagi.';
        }

        // Late order: tgl_ambil hari ini setelah notifikasi batch dikirim (PRD 6.4)
        $isLate = $tglAmbil === Carbon::today()->toDateString()
            && Pesanan::whereDate('tgl_ambil', $tglAmbil)->whereNotNull('notified_at')->exists();

        $pesanan = Pesanan::create([
            'konsumen_id' => $data['konsumen_id'],
            'tgl_pesan' => now(),
            'tgl_ambil' => $tglAmbil,
            'input_source' => 'bot_consumer',
            'status' => Pesanan::STATUS_PENDING,
            'is_late_order' => $isLate,
            'late_order_status' => $isLate ? 'pending_approval' : null,
        ]);

        $baris = [];
        foreach ($keranjang as $item) {
            PesananDetail::create([
                'pesanan_id' => $pesanan->id,
                'produk_id' => $item['produk_id'],
                'qty_pesan' => $item['qty'],
                'satuan' => $item['satuan'],
            ]);
            $baris[] = "- {$item['nama']}: {$item['qty']} {$item['satuan']}";
        }

        $session->reset();

        $msg = "Pesanan Anda telah diterima.\nKode pesanan: *{$pesanan->kode}*\n"
            . implode("\n", $baris)
            . "\nTanggal ambil: {$tglAmbil}\nTunjukkan kode ini saat pengambilan.\nTerima kasih telah berbelanja di Roso Sayur!";

        if ($isLate) {
            $msg .= "\n\nCatatan: pesanan ini masuk sebagai *late order* dan menunggu persetujuan staff.";
        }

        return $msg;
    }

    // ---------- util ----------

    protected function daftarProduk(bool $denganSalam = true): string
    {
        $produks = Produk::where('is_available', true)->where('is_seasonal', false)
            ->orderBy('nama')->get();

        $baris = [];
        foreach ($produks as $i => $p) {
            $baris[] = ($i + 1) . ". {$p->nama} ({$p->satuan})";
        }

        $teks = "Pilih produk yang ingin dipesan (ketik nomor/nama):\n" . implode("\n", $baris);
        if ($denganSalam) {
            $teks .= "\n\nKetik *batal* kapan saja untuk membatalkan.";
        }

        return $teks;
    }

    protected function cariProduk(string $text): ?Produk
    {
        $text = trim($text);

        // by nomor urut
        if (ctype_digit($text)) {
            $produks = Produk::where('is_available', true)->orderBy('nama')->get();

            return $produks->get((int) $text - 1);
        }

        return Produk::where('is_available', true)
            ->where('nama', 'like', "%{$text}%")
            ->orderBy('nama')
            ->first();
    }

    /**
     * Parse format pesan cepat: "kol 3kg" atau "kol 3kg, sawi 2kg".
     * Kembalikan null jika bukan format cepat (fallback ke alur biasa).
     *
     * @return ?array<int, array{nama_input: string, produk_id: ?int, qty: float}>
     */
    protected function parseCepat(string $text): ?array
    {
        $segments = array_values(array_filter(array_map('trim', preg_split('/[,;\n]+/', $text) ?? [])));
        if ($segments === []) {
            return null;
        }

        $items = [];
        foreach ($segments as $seg) {
            if (! preg_match('/^(.+?)\s+(\d+(?:[.,]\d+)?)\s*(kg|kilogram|g|gr|gram|ikat|pcs|pack|buah|butir|sisir|ons)?$/iu', $seg, $m)) {
                return null;
            }
            $nama = trim($m[1]);
            $qty = (float) str_replace(',', '.', $m[2]);
            if ($nama === '' || $qty <= 0) {
                return null;
            }
            $items[] = [
                'nama_input' => $nama,
                'produk_id' => $this->cariProduk($nama)?->id,
                'qty' => $qty,
            ];
        }

        return $items;
    }

    /**
     * Masukkan hasil parse cepat ke keranjang, lanjut ke state tambah.
     */
    protected function tambahCepat(BotSession $session, array $items): string
    {
        $keranjang = $session->keranjang();
        $ok = [];
        $gagal = [];

        foreach ($items as $item) {
            $produk = $item['produk_id'] ? Produk::find($item['produk_id']) : null;
            if (! $produk) {
                $gagal[] = $item['nama_input'];
                continue;
            }
            $keranjang[] = [
                'produk_id' => $produk->id,
                'nama' => $produk->nama,
                'satuan' => $produk->satuan,
                'qty' => $item['qty'],
            ];
            $ok[] = "{$produk->nama} {$item['qty']} {$produk->satuan}";
        }

        if ($ok === []) {
            return $this->gagal($session, "Produk tidak ditemukan. Pilih dari daftar:\n" . $this->daftarProduk());
        }

        $session->sentuh(BotSession::STATE_TAMBAH, ['keranjang' => $keranjang, 'produk_id' => null]);

        $teks = "Ditambahkan:\n- " . implode("\n- ", $ok);
        if ($gagal !== []) {
            $teks .= "\n\nTidak ditemukan: '" . implode("', '", $gagal) . "'.";
        }

        return $teks . "\nIngin tambah produk lain? (Ya/Tidak)";
    }

    protected function ringkasan(BotSession $session): string
    {
        $data = $session->data ?? [];
        $baris = [];
        foreach ($session->keranjang() as $item) {
            $baris[] = "- {$item['nama']}: {$item['qty']} {$item['satuan']}";
        }
        $baris[] = 'Tanggal ambil: ' . ($data['tgl_ambil'] ?? '-');

        return implode("\n", $baris);
    }

    protected function gagal(BotSession $session, string $pesan): string
    {
        $session->increment('percobaan');
        $session->update(['last_activity_at' => now()]);

        if ($session->percobaan >= BotSession::MAX_PERCOBAAN) {
            $session->reset();

            return $pesan . "\n\nTerlalu banyak percobaan gagal. Sesi dihentikan. Ketik *pesan* untuk memulai ulang.";
        }

        return $pesan;
    }

    protected function fallback(BotSession $session): string
    {
        $session->reset();

        return "Terjadi kesalahan sesi. Ketik *pesan* untuk memulai ulang.";
    }
}
