<?php

namespace Tests\Feature;

use App\Models\Konsumen;
use App\Models\Pesanan;
use App\Models\User;
use App\Services\DashboardService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $roso;
    protected Konsumen $konsumen;

    protected function setUp(): void
    {
        parent::setUp();
        $this->roso = User::factory()->create(['role' => 'admin_roso']);
        $this->konsumen = Konsumen::create([
            'nama' => 'Budi',
            'no_hp' => '081234567890',
            'alamat' => 'Jl. Mawar 1',
        ]);
    }

    protected function buatPesanan(string $tglAmbil, string $status = Pesanan::STATUS_PENDING): Pesanan
    {
        return Pesanan::create([
            'konsumen_id' => $this->konsumen->id,
            'tgl_pesan' => Carbon::now(),
            'tgl_ambil' => $tglAmbil,
            'input_source' => 'bot',
            'status' => $status,
        ]);
    }

    public function test_ringkasan_memuat_pesanan_mendatang(): void
    {
        $besok = $this->buatPesanan(Carbon::tomorrow()->toDateString());
        $lusa5 = $this->buatPesanan(Carbon::today()->addDays(5)->toDateString(), Pesanan::STATUS_DITERIMA);
        $hariIni = $this->buatPesanan(Carbon::today()->toDateString());
        $batal = $this->buatPesanan(Carbon::tomorrow()->toDateString(), Pesanan::STATUS_CANCELLED);

        $mendatang = (new DashboardService())->ringkasan()['pesanan_mendatang'];

        $this->assertCount(2, $mendatang);
        // Urut tanggal ambil menaik
        $this->assertTrue($mendatang[0]->is($besok));
        $this->assertTrue($mendatang[1]->is($lusa5));
        // Kode ter-generate otomatis dan ikut dimuat
        $this->assertNotEmpty($mendatang[0]->kode);
        $this->assertSame('Budi', $mendatang[0]->konsumen->nama);
        // Hari ini & yang dibatalkan tidak masuk
        $this->assertFalse($mendatang->contains(fn ($p) => $p->is($hariIni)));
        $this->assertFalse($mendatang->contains(fn ($p) => $p->is($batal)));
    }

    public function test_ringkasan_tanpa_pesanan_mendatang_kosong(): void
    {
        $mendatang = (new DashboardService())->ringkasan()['pesanan_mendatang'];

        $this->assertTrue($mendatang->isEmpty());
    }

    public function test_dashboard_menampilkan_kartu_pesanan_mendatang(): void
    {
        $pesanan = $this->buatPesanan(Carbon::today()->addDays(3)->toDateString());

        $this->actingAs($this->roso)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Pesanan mendatang')
            ->assertSee($pesanan->kode)
            ->assertSee('Budi');
    }

    public function test_dashboard_tanpa_pesanan_mendatang_tampil_pesan_kosong(): void
    {
        $this->actingAs($this->roso)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Tidak ada pesanan mendatang.');
    }
}
