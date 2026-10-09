<?php

namespace Tests\Feature;

use App\Models\KategoriProduk;
use App\Models\Prediksi;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PembelianRekomendasiTest extends TestCase
{
    use RefreshDatabase;

    protected User $roso;

    protected function setUp(): void
    {
        parent::setUp();
        $this->roso = User::factory()->create(['role' => 'admin_roso']);
    }

    protected function buatProduk(): Produk
    {
        $kat = KategoriProduk::create(['nama_kategori' => 'Sayur Daun', 'safety_buffer_persen' => 12]);

        return Produk::create([
            'nama' => 'Kangkung',
            'satuan' => 'ikat',
            'kategori_id' => $kat->id,
            'is_available' => true,
        ]);
    }

    public function test_form_menampilkan_rekomendasi_dan_tombol_pakai(): void
    {
        $produk = $this->buatProduk();
        Prediksi::create([
            'produk_id' => $produk->id,
            'tgl_prediksi' => today()->toDateString(),
            'qty_rekomendasi' => 18.13,
            'qty_dengan_buffer' => 21.00,
        ]);

        $res = $this->actingAs($this->roso)->get(route('pembelian.create', ['tgl' => today()->toDateString()]));

        $res->assertOk();
        $res->assertSee('21.00', false);
        $res->assertSee('Pakai semua rekomendasi', false);
        $res->assertSee('btn-pakai-rekomendasi', false);
        $res->assertSee('btn-pakai-satu', false);
    }

    public function test_form_menampilkan_pesan_saat_prediksi_kosong(): void
    {
        $this->buatProduk();

        $res = $this->actingAs($this->roso)->get(route('pembelian.create', ['tgl' => today()->toDateString()]));

        $res->assertOk();
        $res->assertSee('Belum ada prediksi untuk tanggal', false);
        $res->assertDontSee('Pakai semua rekomendasi', false);
    }
}
