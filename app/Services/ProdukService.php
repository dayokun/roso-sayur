<?php

namespace App\Services;

use App\Models\KategoriProduk;
use App\Models\Produk;
use Illuminate\Database\Eloquent\Collection;

/**
 * Master data produk & kategori (termasuk safety buffer).
 */
class ProdukService
{
    public function daftarProduk(): Collection
    {
        return Produk::with('kategori')->orderBy('nama')->get();
    }

    public function daftarKategori(): Collection
    {
        return KategoriProduk::orderBy('nama_kategori')->get();
    }

    public function simpanProduk(array $data): Produk
    {
        return Produk::create([
            'nama' => $data['nama'],
            'satuan' => $data['satuan'],
            'harga_jual' => $data['harga_jual'] ?? null,
            'kategori_id' => $data['kategori_id'],
            'is_available' => (bool) ($data['is_available'] ?? false),
            'is_seasonal' => (bool) ($data['is_seasonal'] ?? false),
        ]);
    }

    public function ubahProduk(Produk $produk, array $data): Produk
    {
        $produk->update([
            'nama' => $data['nama'],
            'satuan' => $data['satuan'],
            'harga_jual' => $data['harga_jual'] ?? null,
            'kategori_id' => $data['kategori_id'],
            'is_available' => (bool) ($data['is_available'] ?? false),
            'is_seasonal' => (bool) ($data['is_seasonal'] ?? false),
        ]);

        return $produk->fresh();
    }

    public function hapusProduk(Produk $produk): void
    {
        if ($produk->pesananDetails()->exists()) {
            throw new \RuntimeException('Produk tidak bisa dihapus karena sudah ada riwayat pesanan.');
        }
        $produk->delete();
    }

    public function simpanKategori(array $data): KategoriProduk
    {
        return KategoriProduk::create($data);
    }

    public function ubahKategori(KategoriProduk $kategori, array $data): KategoriProduk
    {
        $kategori->update($data);

        return $kategori->fresh();
    }
}
