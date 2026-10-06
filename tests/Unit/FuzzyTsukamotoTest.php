<?php

namespace Tests\Unit;

use App\Services\FuzzyTsukamoto;
use PHPUnit\Framework\TestCase;

/**
 * Verifikasi matematika engine Fuzzy Tsukamoto terhadap
 * perhitungan manual (kasus: a=2, b=5, c=9, pesanan=4, historis=6, tren STABIL).
 */
class FuzzyTsukamotoTest extends TestCase
{
    public function test_fungsi_keanggotaan_dasar(): void
    {
        // turun: 1 di a, 0 di b
        $this->assertEquals(1.0, FuzzyTsukamoto::turun(2, 2, 5));
        $this->assertEquals(0.0, FuzzyTsukamoto::turun(5, 2, 5));
        $this->assertEqualsWithDelta(1 / 3, FuzzyTsukamoto::turun(4, 2, 5), 0.0001);

        // naik: 0 di b, 1 di c
        $this->assertEquals(0.0, FuzzyTsukamoto::naik(5, 5, 9));
        $this->assertEquals(1.0, FuzzyTsukamoto::naik(9, 5, 9));
        $this->assertEqualsWithDelta(0.25, FuzzyTsukamoto::naik(6, 5, 9), 0.0001);

        // segitiga: puncak 1 di b
        $this->assertEquals(1.0, FuzzyTsukamoto::segitiga(5, 2, 5, 9));
        $this->assertEquals(0.0, FuzzyTsukamoto::segitiga(2, 2, 5, 9));
        $this->assertEqualsWithDelta(2 / 3, FuzzyTsukamoto::segitiga(4, 2, 5, 9), 0.0001);
    }

    public function test_invers_output_monoton(): void
    {
        // SEDIKIT turun di [a,b]: alpha 0 -> b, alpha 1 -> a
        $this->assertEqualsWithDelta(5.0, FuzzyTsukamoto::invers('SEDIKIT', 0, 2, 5, 9), 0.0001);
        $this->assertEqualsWithDelta(2.0, FuzzyTsukamoto::invers('SEDIKIT', 1, 2, 5, 9), 0.0001);
        // SEDANG turun di [b,c]
        $this->assertEqualsWithDelta(9.0, FuzzyTsukamoto::invers('SEDANG', 0, 2, 5, 9), 0.0001);
        $this->assertEqualsWithDelta(5.0, FuzzyTsukamoto::invers('SEDANG', 1, 2, 5, 9), 0.0001);
        // BANYAK naik di [b,c]
        $this->assertEqualsWithDelta(5.0, FuzzyTsukamoto::invers('BANYAK', 0, 2, 5, 9), 0.0001);
        $this->assertEqualsWithDelta(9.0, FuzzyTsukamoto::invers('BANYAK', 1, 2, 5, 9), 0.0001);
    }

    public function test_klasifikasi_tren(): void
    {
        $this->assertEquals(['TURUN' => 1.0, 'STABIL' => 0.0, 'NAIK' => 0.0], FuzzyTsukamoto::tren(-0.15));
        $this->assertEquals(['TURUN' => 0.0, 'STABIL' => 1.0, 'NAIK' => 0.0], FuzzyTsukamoto::tren(0.05));
        $this->assertEquals(['TURUN' => 0.0, 'STABIL' => 0.0, 'NAIK' => 1.0], FuzzyTsukamoto::tren(0.2));
    }

    public function test_inferensi_contoh_manual(): void
    {
        $fuzzy = new FuzzyTsukamoto();
        $mf = ['a' => 2.0, 'b' => 5.0, 'c' => 9.0];

        $hasil = $fuzzy->infer(4.0, 6.0, 0.05, $mf, $mf);

        // Rule aktif yang diharapkan: R5, R8, R14, R17
        $ruleIds = array_column($hasil['rules'], 'rule_id');
        sort($ruleIds);
        $this->assertEquals([5, 8, 14, 17], $ruleIds);

        // z* = (1/3*4 + 1/4*8 + 2/3*6.3333 + 1/4*6) / 1.5 = 6.037037...
        $this->assertEqualsWithDelta(6.04, $hasil['qty_rekomendasi'], 0.01);

        // Spot-check R14: alpha = min(2/3, 3/4, 1) = 2/3, z = 9 - 2/3*4
        $r14 = collect($hasil['rules'])->firstWhere('rule_id', 14);
        $this->assertEqualsWithDelta(0.6667, $r14['alpha_value'], 0.001);
        $this->assertEquals('SEDANG', $r14['output_term']);
        $this->assertEqualsWithDelta(6.3333, $r14['z_value'], 0.001);
    }

    public function test_r19_output_sedang(): void
    {
        $rules = FuzzyTsukamoto::rules();
        $this->assertCount(27, $rules);

        $r19 = $rules[18]; // index 18 = R19
        $this->assertEquals(['BANYAK', 'RENDAH', 'TURUN', 'SEDANG'], $r19);
    }
}
