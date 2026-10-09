<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Integrasi WhatsApp via Fonnte API.
 *
 * Mode fake (FONNTE_FAKE=true atau token kosong): pesan tidak benar-benar
 * dikirim, hanya dicatat ke log — untuk development & testing.
 */
class FonnteService
{
    protected string $token;
    protected bool $fake;

    public function __construct()
    {
        $this->token = (string) config('services.fonnte.token', '');
        $this->fake = (bool) config('services.fonnte.fake', true);
    }

    public function isFake(): bool
    {
        return $this->fake || $this->token === '';
    }

    /**
     * Kirim pesan teks ke satu nomor.
     *
     * @return array{ok: bool, detail: string}
     */
    public function kirim(string $noHp, string $pesan): array
    {
        $noHp = $this->normalisasi($noHp);

        if ($this->isFake()) {
            Log::channel('fonnte')->info("[FAKE] ke {$noHp}: {$pesan}");

            return ['ok' => true, 'detail' => 'fake-sent'];
        }

        try {
            $res = Http::asForm()
                ->withHeaders(['Authorization' => $this->token])
                ->timeout(10)
                ->post('https://api.fonnte.com/send', [
                    'target' => $noHp,
                    'message' => $pesan,
                ]);

            $ok = $res->successful();
            if (! $ok) {
                Log::channel('fonnte')->warning("Gagal kirim ke {$noHp}: {$res->body()}");
            }

            return ['ok' => $ok, 'detail' => $res->body()];
        } catch (\Throwable $e) {
            Log::channel('fonnte')->error("Exception kirim ke {$noHp}: {$e->getMessage()}");

            return ['ok' => false, 'detail' => $e->getMessage()];
        }
    }

    /**
     * Kirim file (PDF/gambar) ke satu nomor via URL publik.
     *
     * @return array{ok: bool, detail: string}
     */
    public function kirimFile(string $noHp, string $fileUrl, string $filename, string $caption = ''): array
    {
        $noHp = $this->normalisasi($noHp);

        if ($this->isFake()) {
            Log::channel('fonnte')->info("[FAKE] file ke {$noHp}: {$filename} ({$fileUrl}) caption: {$caption}");

            return ['ok' => true, 'detail' => 'fake-sent'];
        }

        try {
            $payload = [
                'target' => $noHp,
                'url' => $fileUrl,
                'filename' => $filename,
            ];
            if ($caption !== '') {
                $payload['message'] = $caption;
            }

            $res = Http::asForm()
                ->withHeaders(['Authorization' => $this->token])
                ->timeout(15)
                ->post('https://api.fonnte.com/send', $payload);

            $ok = $res->successful();
            if (! $ok) {
                Log::channel('fonnte')->warning("Gagal kirim file ke {$noHp}: {$res->body()}");
            }

            return ['ok' => $ok, 'detail' => $res->body()];
        } catch (\Throwable $e) {
            Log::channel('fonnte')->error("Exception kirim file ke {$noHp}: {$e->getMessage()}");

            return ['ok' => false, 'detail' => $e->getMessage()];
        }
    }

    /**
     * Normalisasi ke format 628xx (tanpa +, tanpa spasi).
     */
    public function normalisasi(string $noHp): string
    {
        $n = preg_replace('/[^0-9]/', '', $noHp);
        if (str_starts_with($n, '0')) {
            $n = '62' . substr($n, 1);
        }

        return $n;
    }
}
