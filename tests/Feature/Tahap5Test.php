<?php

namespace Tests\Feature;

use App\Models\KategoriProduk;
use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\Produk;
use App\Models\SisaStok;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Tahap5Test extends TestCase
{
    use RefreshDatabase;

    protected User $roso;

    protected function setUp(): void
    {
        parent::setUp();
        $this->roso = User::factory()->create(['role' => 'admin_roso']);
    }

    public function test_dashboard_bisa_dirender(): void
    {
        $this->actingAs($this->roso)->get('/dashboard')->assertOk();
    }

    public function test_export_excel_menghasilkan_file(): void
    {
        $res = $this->actingAs($this->roso)->get('/laporan/export-excel');
        $res->assertOk();
        $this->assertStringContainsString(
            'spreadsheetml',
            $res->headers->get('Content-Type')
        );
    }

    public function test_export_pdf_menghasilkan_file(): void
    {
        $res = $this->actingAs($this->roso)->get('/laporan/export-pdf');
        $res->assertOk();
        $this->assertEquals('application/pdf', $res->headers->get('Content-Type'));
    }

    public function test_waste_rate_dihitung_benar(): void
    {
        $kat = KategoriProduk::create(['nama_kategori' => 'Kat', 'safety_buffer_persen' => 5]);
        $produk = Produk::create([
            'nama' => 'Wortel', 'satuan' => 'kg', 'kategori_id' => $kat->id,
            'is_available' => true, 'is_seasonal' => false,
        ]);
        $pembelian = Pembelian::create(['tgl_beli' => Carbon::today()->toDateString()]);
        $detail = PembelianDetail::create([
            'pembelian_id' => $pembelian->id, 'produk_id' => $produk->id,
            'is_available_today' => true, 'qty_beli' => 10,
        ]);
        SisaStok::create([
            'pembelian_detail_id' => $detail->id, 'tgl' => Carbon::today()->toDateString(),
            'qty_sisa' => 2, 'status_sisa' => 'dibuang', 'qty_dibuang' => 2,
        ]);

        $ringkasan = (new \App\Services\EvaluasiService())
            ->ringkasan(Carbon::today(), Carbon::today());

        $wr = $ringkasan['waste_rate']->keyBy('nama')['Wortel'];
        $this->assertEquals(20.0, round($wr->total_dibuang / $wr->total_beli * 100, 2));
    }

    public function test_laporan_menampilkan_tabel_waste_rate(): void
    {
        $this->actingAs($this->roso)->get('/laporan')->assertOk()->assertSee('Waste Rate');
    }
}
