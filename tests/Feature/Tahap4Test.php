<?php

namespace Tests\Feature;

use App\Models\KategoriProduk;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Tahap4Test extends TestCase
{
    use RefreshDatabase;

    protected User $adminIt;
    protected User $roso;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminIt = User::factory()->create(['role' => 'admin_it']);
        $this->roso = User::factory()->create(['role' => 'admin_roso']);
    }

    public function test_role_middleware_memblokir_admin_roso_dari_menu_admin_it(): void
    {
        $this->actingAs($this->roso)->get('/pengguna')->assertForbidden();
        $this->actingAs($this->roso)->get('/notifikasi/unlock')->assertForbidden();
        $this->actingAs($this->roso)->get('/notifikasi/log')->assertForbidden();
    }

    public function test_admin_it_bisa_akses_menu_admin_it(): void
    {
        $this->actingAs($this->adminIt)->get('/pengguna')->assertOk();
        $this->actingAs($this->adminIt)->get('/notifikasi/log')->assertOk();
    }

    public function test_menu_baru_bisa_dirender(): void
    {
        foreach (['/produk', '/konsumen', '/prediksi', '/sisa-stok', '/pesanan', '/pembelian', '/notifikasi', '/laporan'] as $uri) {
            $this->actingAs($this->roso)->get($uri)->assertOk($uri);
        }
    }

    public function test_registrasi_publik_dimatikan(): void
    {
        $this->get('/register')->assertNotFound();
        $this->post('/register', [])->assertNotFound();
    }

    public function test_produk_crud_lewat_service(): void
    {
        $svc = new \App\Services\ProdukService();
        $kat = KategoriProduk::create(['nama_kategori' => 'Test Kat', 'safety_buffer_persen' => 5]);

        $p = $svc->simpanProduk(['nama' => 'Produk X', 'satuan' => 'kg', 'kategori_id' => $kat->id, 'is_available' => true]);
        $this->assertDatabaseHas('produk', ['nama' => 'Produk X']);

        $svc->ubahProduk($p, ['nama' => 'Produk X', 'satuan' => 'ikat', 'kategori_id' => $kat->id, 'is_available' => true]);
        $this->assertEquals('ikat', $p->fresh()->satuan);

        $svc->hapusProduk($p);
        $this->assertDatabaseMissing('produk', ['nama' => 'Produk X']);
    }

    public function test_user_service_hash_password_dan_tolak_hapus_diri(): void
    {
        $svc = new \App\Services\UserService();
        $u = $svc->simpan(['name' => 'Test', 'email' => 't@t.test', 'password' => 'password123', 'role' => 'admin_roso']);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('password123', $u->password));

        $this->expectException(\RuntimeException::class);
        $svc->hapus($this->adminIt, $this->adminIt);
    }

    public function test_unlock_form_request_hanya_untuk_admin_it(): void
    {
        // admin_roso tidak lolos authorize
        $this->actingAs($this->roso)
            ->post('/notifikasi/unlock', ['tgl' => now()->toDateString(), 'alasan' => 'alasan yang cukup panjang'])
            ->assertForbidden();
    }
}
