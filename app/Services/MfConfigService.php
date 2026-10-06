<?php

namespace App\Services;

use App\Models\PesananDetail;
use App\Models\Produk;
use App\Models\ProdukMfConfig;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Generate & versioning konfigurasi Membership Function per produk.
 * PRD 6.6.2: a = P33, b = P66, c = P95 dari qty_pesan historis.
 * Butuh minimal 30 hari data historis, jika tidak -> null (prediksi di-skip).
 */
class MfConfigService
{
    public const MIN_HARI_HISTORIS = 30;

    /**
     * Hitung persentil dengan interpolasi linear.
     */
    public static function persentil(array $data, float $p): float
    {
        $data = array_values(array_filter($data, fn ($v) => is_numeric($v)));
        sort($data);
        $n = count($data);
        if ($n === 0) {
            return 0.0;
        }
        if ($n === 1) {
            return (float) $data[0];
        }
        $rank = ($p / 100) * ($n - 1);
        $lo = (int) floor($rank);
        $hi = (int) ceil($rank);
        $frac = $rank - $lo;

        return (float) ($data[$lo] + $frac * ($data[$hi] - $data[$lo]));
    }

    /**
     * Total qty_pesan harian per produk (kecualikan anomali), N hari ke belakang.
     *
     * @return array<string, float>  ['Y-m-d' => total]
     */
    public static function historiHarian(int $produkId, int $hari = 90): array
    {
        $rows = PesananDetail::query()
            ->join('pesanan', 'pesanan.id', '=', 'pesanan_detail.pesanan_id')
            ->where('pesanan_detail.produk_id', $produkId)
            ->where('pesanan_detail.is_anomali', false)
            ->where('pesanan.tgl_pesan', '>=', Carbon::today()->subDays($hari)->startOfDay())
            ->groupBy(DB::raw('DATE(pesanan.tgl_pesan)'))
            ->selectRaw('DATE(pesanan.tgl_pesan) as tgl, SUM(pesanan_detail.qty_pesan) as total')
            ->pluck('total', 'tgl')
            ->toArray();

        return array_map('floatval', $rows);
    }

    /**
     * Generate konfigurasi MF baru untuk sebuah produk (versioning).
     * Menutup versi lama (valid_until = kemarin) dan membuat versi baru.
     *
     * @return array{pesanan: ProdukMfConfig, historis: ProdukMfConfig}|null
     */
    public function generate(Produk $produk, int $hariHistori = 90): ?array
    {
        $histori = self::historiHarian($produk->id, $hariHistori);

        if (count($histori) < self::MIN_HARI_HISTORIS) {
            return null; // data belum cukup -> prediksi di-skip (PRD 6.6.2)
        }

        $totals = array_values($histori);
        $bounds = [
            'a' => round(self::persentil($totals, 33), 2),
            'b' => round(self::persentil($totals, 66), 2),
            'c' => round(self::persentil($totals, 95), 2),
        ];

        // Pastikan a < b < c (antisipasi data flat)
        if ($bounds['b'] <= $bounds['a']) {
            $bounds['b'] = $bounds['a'] + 0.5;
        }
        if ($bounds['c'] <= $bounds['b']) {
            $bounds['c'] = $bounds['b'] + 0.5;
        }

        $today = Carbon::today()->toDateString();
        $result = [];

        foreach ([ProdukMfConfig::VARIABEL_PESANAN, ProdukMfConfig::VARIABEL_HISTORIS] as $variabel) {
            // Tutup versi aktif sebelumnya
            ProdukMfConfig::where('produk_id', $produk->id)
                ->where('variabel', $variabel)
                ->where('valid_from', '<=', $today)
                ->where(function ($q) use ($today) {
                    $q->whereNull('valid_until')->orWhere('valid_until', '>=', $today);
                })
                ->update(['valid_until' => Carbon::today()->subDay()->toDateString()]);

            $result[$variabel] = ProdukMfConfig::create([
                'produk_id' => $produk->id,
                'variabel' => $variabel,
                'batas_bawah' => $bounds['a'],
                'batas_tengah' => $bounds['b'],
                'batas_atas' => $bounds['c'],
                'generated_from' => count($histori) . ' hari histori',
                'valid_from' => $today,
                'valid_until' => null,
                'generated_at' => now(),
            ]);
        }

        return $result;
    }
}
