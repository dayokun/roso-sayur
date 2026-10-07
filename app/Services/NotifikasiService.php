<?php

namespace App\Services;

use App\Models\NotifikasiUnlockLog;
use App\Models\PembelianDetail;
use App\Models\Pesanan;
use App\Models\PesananDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Notifikasi batch "Proses & Kirim Notifikasi" (PRD 6.3, 6.5).
 * Evaluasi agregat per produk (bukan per konsumen) — fix v1.3.
 */
class NotifikasiService
{
    public const SKENARIO_AMAN = 'aman';
    public const SKENARIO_TERBATAS = 'terbatas';
    public const SKENARIO_KOSONG = 'tidak_tersedia';

    public const MAX_UNLOCK_PER_HARI = 2;

    public function __construct(protected FonnteService $fonnte = new FonnteService()) {}

    /**
     * Ringkasan preview sebelum kirim (PRD 6.3 step 1 — fix v1.3).
     *
     * @return array{produk: array, belum_diinput: array, terkunci: bool}
     */
    public function preview(Carbon $tgl): array
    {
        $tglStr = $tgl->toDateString();
        $produk = $this->evaluasiProduk($tglStr);

        $belum = (new PembelianService())->belumDiinput($tgl);
        $terkunci = Pesanan::whereDate('tgl_ambil', $tglStr)->whereNotNull('notified_at')->exists();

        return ['produk' => $produk, 'belum_diinput' => $belum, 'terkunci' => $terkunci];
    }

    /**
     * Kirim notifikasi batch. Melempar exception jika pre-check/guard gagal.
     *
     * @return array{terkirim: int, detail: array}
     */
    public function kirim(Carbon $tgl): array
    {
        $tglStr = $tgl->toDateString();

        // Pre-check: semua produk yang dipesan sudah diinput actual beli
        $belum = (new PembelianService())->belumDiinput($tgl);
        if (! empty($belum)) {
            throw new \RuntimeException(
                'Belum semua produk diinput actual beli: ' . implode(', ', $belum) . '. Selesaikan input sebelum kirim notifikasi.'
            );
        }

        // Guard duplikat via notified_at
        if (Pesanan::whereDate('tgl_ambil', $tglStr)->whereNotNull('notified_at')->exists()) {
            throw new \RuntimeException('Notifikasi hari ini sudah dikirim dan tombol terkunci. Minta admin untuk unlock.');
        }

        $evaluasi = $this->evaluasiProduk($tglStr);
        $terkirim = 0;

        DB::transaction(function () use ($tglStr, $evaluasi, &$terkirim) {
            $perKonsumen = Pesanan::with(['konsumen', 'details.produk'])
                ->whereDate('tgl_ambil', $tglStr)
                ->whereIn('status', [Pesanan::STATUS_PENDING, Pesanan::STATUS_DITERIMA, Pesanan::STATUS_SIAP_DIAMBIL])
                ->whereNull('notified_at')
                ->get()
                ->groupBy('konsumen_id');

            // Satu pesan per konsumen: gabung semua kode pesanannya
            foreach ($perKonsumen as $pesanans) {
                $blok = [];
                foreach ($pesanans as $pesanan) {
                    $baris = [];
                    foreach ($pesanan->details as $detail) {
                        $ev = $evaluasi[$detail->produk_id] ?? null;
                        if (! $ev) {
                            continue;
                        }
                        $baris[] = $this->pesanProduk($detail->produk->nama, $ev['skenario']);
                    }

                    if ($baris) {
                        $kode = $pesanan->kode ?? "#{$pesanan->id}";
                        $blok[] = "*{$kode}*\n" . implode("\n", $baris);
                    }

                    $pesanan->update([
                        'status' => Pesanan::STATUS_SIAP_DIAMBIL,
                        'notified_at' => now(),
                    ]);
                }

                if ($blok) {
                    $pesan = "Pesanan Anda siap diambil. Tunjukkan kode saat pengambilan.\n\n"
                        . implode("\n\n", $blok);
                    $this->fonnte->kirim($pesanans->first()->konsumen->no_hp, $pesan);
                    $terkirim++;
                }
            }
        });

        return ['terkirim' => $terkirim, 'detail' => array_values($evaluasi)];
    }

    /**
     * Unlock tombol notifikasi (hanya Admin IT). Melempar exception jika tidak valid.
     */
    public function unlock(Carbon $tgl, User $admin, string $alasan): NotifikasiUnlockLog
    {
        if (! $admin->isAdminIt()) {
            throw new \RuntimeException('Hanya Admin IT yang dapat unlock tombol notifikasi.');
        }
        if (mb_strlen(trim($alasan)) < 10) {
            throw new \RuntimeException('Alasan unlock wajib diisi minimal 10 karakter.');
        }

        $tglStr = $tgl->toDateString();
        $jumlah = NotifikasiUnlockLog::whereDate('tgl', $tglStr)->count();
        if ($jumlah >= self::MAX_UNLOCK_PER_HARI) {
            throw new \RuntimeException('Batas unlock harian (2x) tercapai. Hubungi admin level lebih tinggi.');
        }

        return DB::transaction(function () use ($tgl, $tglStr, $admin, $alasan) {
            $log = NotifikasiUnlockLog::create([
                'admin_id' => $admin->id,
                'tgl' => $tglStr,
                'alasan' => trim($alasan),
                'timestamp_unlock' => now(),
            ]);

            $terdampak = Pesanan::with('konsumen')
                ->whereDate('tgl_ambil', $tglStr)
                ->whereNotNull('notified_at')
                ->get();

            Pesanan::whereDate('tgl_ambil', $tglStr)->update(['notified_at' => null]);

            foreach ($terdampak as $pesanan) {
                $this->fonnte->kirim(
                    $pesanan->konsumen->no_hp,
                    'Mohon abaikan notifikasi sebelumnya. Kami mengirimkan update terbaru untuk pesanan Anda.'
                );
            }

            return $log;
        });
    }

    /**
     * Evaluasi agregat per produk: bandingkan qty_beli vs total qty_pesan.
     *
     * @return array<int, array{produk_id: string, nama: string, qty_beli: float, total_pesan: float, skenario: string}>
     */
    protected function evaluasiProduk(string $tglStr): array
    {
        $totals = PesananDetail::query()
            ->join('pesanan', 'pesanan.id', '=', 'pesanan_detail.pesanan_id')
            ->join('produk', 'produk.id', '=', 'pesanan_detail.produk_id')
            ->whereDate('pesanan.tgl_ambil', $tglStr)
            ->where('pesanan.status', '!=', Pesanan::STATUS_CANCELLED)
            ->groupBy('pesanan_detail.produk_id', 'produk.nama')
            ->selectRaw('pesanan_detail.produk_id, produk.nama, SUM(pesanan_detail.qty_pesan) as total')
            ->get();

        $hasil = [];
        foreach ($totals as $row) {
            $beli = PembelianDetail::query()
                ->join('pembelian', 'pembelian.id', '=', 'pembelian_detail.pembelian_id')
                ->whereDate('pembelian.tgl_beli', $tglStr)
                ->where('pembelian_detail.produk_id', $row->produk_id)
                ->first();

            $qtyBeli = $beli ? (float) $beli->qty_beli : 0.0;
            $tersedia = $beli ? (bool) $beli->is_available_today : false;
            $total = (float) $row->total;

            $skenario = self::SKENARIO_TERBATAS;
            if (! $tersedia || $qtyBeli == 0) {
                $skenario = self::SKENARIO_KOSONG;
            } elseif ($qtyBeli >= $total) {
                $skenario = self::SKENARIO_AMAN;
            }

            $hasil[$row->produk_id] = [
                'produk_id' => $row->produk_id,
                'nama' => $row->nama,
                'qty_beli' => $qtyBeli,
                'total_pesan' => $total,
                'skenario' => $skenario,
            ];
        }

        return $hasil;
    }

    protected function pesanProduk(string $nama, string $skenario): string
    {
        return match ($skenario) {
            self::SKENARIO_AMAN => "- {$nama}: stok aman tersedia.",
            self::SKENARIO_TERBATAS => "- {$nama}: stok terbatas, dilayani sesuai urutan kedatangan fisik ke toko.",
            self::SKENARIO_KOSONG => "- {$nama}: mohon maaf tidak tersedia hari ini.",
        };
    }

    /**
     * Riwayat audit unlock untuk dashboard.
     */
    public function daftarLog(): \Illuminate\Database\Eloquent\Collection
    {
        return NotifikasiUnlockLog::with('admin')->latest('timestamp_unlock')->get();
    }
}
