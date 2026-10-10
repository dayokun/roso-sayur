<?php

namespace Tests\Feature;

use App\Models\KategoriProduk;
use App\Models\Konsumen;
use App\Models\Pesanan;
use App\Models\PesananDetail;
use App\Models\Produk;
use App\Models\User;
use App\Services\InvoiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_pdf_tergenerate_dengan_total_benar(): void
    {
        Storage::fake('public');

        $kat = KategoriProduk::create(['nama_kategori' => 'Sayur Daun', 'safety_buffer_persen' => 12]);
        $produk = Produk::create([
            'nama' => 'Kangkung', 'satuan' => 'ikat', 'kategori_id' => $kat->id,
            'harga_jual' => 5000, 'is_available' => true,
        ]);
        $konsumen = Konsumen::create(['nama' => 'Budi', 'no_hp' => '08123456789']);

        $pesanan = Pesanan::create([
            'konsumen_id' => $konsumen->id,
            'tgl_pesan' => today(),
            'tgl_ambil' => today()->addDay(),
            'status' => Pesanan::STATUS_SELESAI,
            'kode' => 'RS-20261009-0001',
        ]);
        PesananDetail::create([
            'pesanan_id' => $pesanan->id,
            'produk_id' => $produk->id,
            'qty_pesan' => 3, 'qty_delivered' => 3, 'satuan' => 'ikat',
        ]);

        $hasil = app(InvoiceService::class)->buatPdf($pesanan);

        $this->assertEquals(15000.0, $hasil['total']);
        $this->assertEquals('kwitansi-RS-20261009-0001.pdf', $hasil['filename']);
        Storage::disk('public')->assertExists($hasil['path']);
    }

    public function test_form_produk_bisa_disubmit_dengan_harga_jual(): void
    {
        $admin = User::factory()->create(['role' => 'admin_it']);
        $kat = KategoriProduk::create(['nama_kategori' => 'Sayur Daun', 'safety_buffer_persen' => 12]);

        $res = $this->actingAs($admin)->post(route('produk.store'), [
            'nama' => 'Bayam',
            'satuan' => 'ikat',
            'kategori_id' => $kat->id,
            'harga_jual' => 4000,
        ]);

        $res->assertRedirect();
        $this->assertEquals(4000, Produk::where('nama', 'Bayam')->first()->harga_jual);
    }
}
