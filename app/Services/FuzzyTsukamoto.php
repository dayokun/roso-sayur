<?php

namespace App\Services;

/**
 * Mesin inferensi Fuzzy Tsukamoto sesuai PRD v1.3 Section 6.6.
 *
 * - 3 variabel input: Pesanan (SEDIKIT/SEDANG/BANYAK),
 *   Historis 7 hari (RENDAH/SEDANG/TINGGI), Tren (TURUN/STABIL/NAIK).
 * - Output monoton (syarat Tsukamoto): SEDIKIT turun di [a,b],
 *   SEDANG turun di [b,c], BANYAK naik di [b,c] — ketiganya invertible.
 * - 27 rules sesuai tabel PRD 6.6.4 (termasuk koreksi R19 -> SEDANG).
 * - Defuzzifikasi: weighted average z* = SUM(ai * zi) / SUM(ai).
 */
class FuzzyTsukamoto
{
    /**
     * Derajat keanggotaan linear TURUN: 1 di a, 0 di b.
     */
    public static function turun(float $x, float $a, float $b): float
    {
        if ($b <= $a) {
            return $x <= $a ? 1.0 : 0.0;
        }
        if ($x <= $a) {
            return 1.0;
        }
        if ($x >= $b) {
            return 0.0;
        }

        return ($b - $x) / ($b - $a);
    }

    /**
     * Derajat keanggotaan linear NAIK: 0 di b, 1 di c.
     */
    public static function naik(float $x, float $b, float $c): float
    {
        if ($c <= $b) {
            return $x >= $c ? 1.0 : 0.0;
        }
        if ($x <= $b) {
            return 0.0;
        }
        if ($x >= $c) {
            return 1.0;
        }

        return ($x - $b) / ($c - $b);
    }

    /**
     * Derajat keanggotaan SEGITIGA: 0 di a dan c, puncak 1 di b.
     */
    public static function segitiga(float $x, float $a, float $b, float $c): float
    {
        if ($x <= $a || $x >= $c) {
            return 0.0;
        }
        if ($x <= $b) {
            return $b == $a ? 1.0 : ($x - $a) / ($b - $a);
        }

        return $c == $b ? 1.0 : ($c - $x) / ($c - $b);
    }

    /**
     * Invers fungsi keanggotaan OUTPUT (monoton) -> nilai crisp z
     * untuk firing strength alpha. Syarat validitas Tsukamoto.
     */
    public static function invers(string $term, float $alpha, float $a, float $b, float $c): float
    {
        $alpha = max(0.0, min(1.0, $alpha));

        return match ($term) {
            'SEDIKIT' => $b - $alpha * ($b - $a), // turun di [a,b]
            'SEDANG' => $c - $alpha * ($c - $b),  // turun di [b,c]
            'BANYAK' => $b + $alpha * ($c - $b),  // naik di [b,c]
        };
    }

    /**
     * Klasifikasi tren sesuai PRD 6.6.1: TURUN jika delta < -10% rata-rata,
     * NAIK jika delta > +10%, selain itu STABIL.
     *
     * @return array{TURUN: float, STABIL: float, NAIK: float}
     */
    public static function tren(float $deltaRatio): array
    {
        if ($deltaRatio < -0.10) {
            return ['TURUN' => 1.0, 'STABIL' => 0.0, 'NAIK' => 0.0];
        }
        if ($deltaRatio > 0.10) {
            return ['TURUN' => 0.0, 'STABIL' => 0.0, 'NAIK' => 1.0];
        }

        return ['TURUN' => 0.0, 'STABIL' => 1.0, 'NAIK' => 0.0];
    }

    /**
     * Rule base 27 rules: [term_pesanan, term_historis, term_tren, term_output].
     * Sesuai PRD 6.6.4 (v1.3, R19 = SEDANG).
     */
    public static function rules(): array
    {
        return [
            // Kelompok A — Pesanan SEDIKIT (R1-R9)
            ['SEDIKIT', 'RENDAH', 'TURUN', 'SEDIKIT'],  // R1
            ['SEDIKIT', 'RENDAH', 'STABIL', 'SEDIKIT'], // R2
            ['SEDIKIT', 'RENDAH', 'NAIK', 'SEDIKIT'],   // R3
            ['SEDIKIT', 'SEDANG', 'TURUN', 'SEDIKIT'],   // R4
            ['SEDIKIT', 'SEDANG', 'STABIL', 'SEDIKIT'],  // R5
            ['SEDIKIT', 'SEDANG', 'NAIK', 'SEDANG'],     // R6
            ['SEDIKIT', 'TINGGI', 'TURUN', 'SEDANG'],    // R7
            ['SEDIKIT', 'TINGGI', 'STABIL', 'SEDANG'],   // R8
            ['SEDIKIT', 'TINGGI', 'NAIK', 'SEDANG'],     // R9
            // Kelompok B — Pesanan SEDANG (R10-R18)
            ['SEDANG', 'RENDAH', 'TURUN', 'SEDIKIT'],  // R10
            ['SEDANG', 'RENDAH', 'STABIL', 'SEDIKIT'], // R11
            ['SEDANG', 'RENDAH', 'NAIK', 'SEDANG'],    // R12
            ['SEDANG', 'SEDANG', 'TURUN', 'SEDANG'],    // R13
            ['SEDANG', 'SEDANG', 'STABIL', 'SEDANG'],   // R14
            ['SEDANG', 'SEDANG', 'NAIK', 'SEDANG'],     // R15
            ['SEDANG', 'TINGGI', 'TURUN', 'SEDANG'],    // R16
            ['SEDANG', 'TINGGI', 'STABIL', 'BANYAK'],  // R17
            ['SEDANG', 'TINGGI', 'NAIK', 'BANYAK'],     // R18
            // Kelompok C — Pesanan BANYAK (R19-R27)
            ['BANYAK', 'RENDAH', 'TURUN', 'SEDANG'],  // R19 (koreksi v1.3)
            ['BANYAK', 'RENDAH', 'STABIL', 'SEDANG'], // R20
            ['BANYAK', 'RENDAH', 'NAIK', 'SEDANG'],   // R21
            ['BANYAK', 'SEDANG', 'TURUN', 'SEDANG'],  // R22
            ['BANYAK', 'SEDANG', 'STABIL', 'BANYAK'], // R23
            ['BANYAK', 'SEDANG', 'NAIK', 'BANYAK'],   // R24
            ['BANYAK', 'TINGGI', 'TURUN', 'SEDANG'],  // R25
            ['BANYAK', 'TINGGI', 'STABIL', 'BANYAK'], // R26
            ['BANYAK', 'TINGGI', 'NAIK', 'BANYAK'],   // R27
        ];
    }

    /**
     * Jalankan inferensi lengkap.
     *
     * @param  array{a: float, b: float, c: float}  $mfPesanan  Batas MF variabel pesanan
     * @param  array{a: float, b: float, c: float}  $mfHistoris  Batas MF variabel historis
     * @return array{qty_rekomendasi: float, rules: array, mu: array}
     */
    public function infer(float $pesanan, float $historis, float $deltaRatio, array $mfPesanan, array $mfHistoris): array
    {
        [$aP, $bP, $cP] = [$mfPesanan['a'], $mfPesanan['b'], $mfPesanan['c']];
        [$aH, $bH, $cH] = [$mfHistoris['a'], $mfHistoris['b'], $mfHistoris['c']];

        $muP = [
            'SEDIKIT' => self::turun($pesanan, $aP, $bP),
            'SEDANG' => self::segitiga($pesanan, $aP, $bP, $cP),
            'BANYAK' => self::naik($pesanan, $bP, $cP),
        ];
        $muH = [
            'RENDAH' => self::turun($historis, $aH, $bH),
            'SEDANG' => self::segitiga($historis, $aH, $bH, $cH),
            'TINGGI' => self::naik($historis, $bH, $cH),
        ];
        $muT = self::tren($deltaRatio);

        $fired = [];
        $sumAlphaZ = 0.0;
        $sumAlpha = 0.0;

        foreach (self::rules() as $i => [$tp, $th, $tt, $out]) {
            $alpha = min($muP[$tp], $muH[$th], $muT[$tt]);
            if ($alpha <= 0) {
                continue; // rule tidak aktif, tidak dicatat
            }
            // Output memakai domain batas MF pesanan (skala kg harian)
            $z = self::invers($out, $alpha, $aP, $bP, $cP);
            $fired[] = [
                'rule_id' => $i + 1,
                'alpha_pesanan' => round($muP[$tp], 4),
                'alpha_historis' => round($muH[$th], 4),
                'alpha_tren' => round($muT[$tt], 4),
                'alpha_value' => round($alpha, 4),
                'output_term' => $out,
                'z_value' => round($z, 4),
                'bobot' => round($alpha * $z, 4),
            ];
            $sumAlphaZ += $alpha * $z;
            $sumAlpha += $alpha;
        }

        $zStar = $sumAlpha > 0 ? $sumAlphaZ / $sumAlpha : 0.0;

        return [
            'qty_rekomendasi' => round($zStar, 2),
            'rules' => $fired,
            'mu' => ['pesanan' => $muP, 'historis' => $muH, 'tren' => $muT],
        ];
    }
}
