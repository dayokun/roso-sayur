<?php

namespace Database\Seeders;

use App\Models\KategoriProduk;
use App\Models\Produk;
use Illuminate\Database\Seeder;

class ProdukSeeder extends Seeder
{
    public function run(): void
    {
        $kat = KategoriProduk::pluck('id', 'nama_kategori');

        $data = [
            // Sayur Daun
            ['nama' => 'Kangkung', 'satuan' => 'ikat', 'kategori' => 'Sayur Daun'],
            ['nama' => 'Bayam', 'satuan' => 'ikat', 'kategori' => 'Sayur Daun'],
            ['nama' => 'Sawi Hijau', 'satuan' => 'kg', 'kategori' => 'Sayur Daun'],
            ['nama' => 'Kol', 'satuan' => 'kg', 'kategori' => 'Sayur Daun'],
            // Sayur Umbi & Buah
            ['nama' => 'Wortel', 'satuan' => 'kg', 'kategori' => 'Sayur Umbi & Buah'],
            ['nama' => 'Kentang', 'satuan' => 'kg', 'kategori' => 'Sayur Umbi & Buah'],
            ['nama' => 'Tomat', 'satuan' => 'kg', 'kategori' => 'Sayur Umbi & Buah'],
            ['nama' => 'Cabai Merah', 'satuan' => 'kg', 'kategori' => 'Sayur Umbi & Buah'],
            ['nama' => 'Bawang Merah', 'satuan' => 'kg', 'kategori' => 'Sayur Umbi & Buah'],
            // Buah Lokal
            ['nama' => 'Pisang Kepok', 'satuan' => 'sisir', 'kategori' => 'Buah Lokal'],
            ['nama' => 'Jeruk Medan', 'satuan' => 'kg', 'kategori' => 'Buah Lokal'],
            ['nama' => 'Durian', 'satuan' => 'buah', 'kategori' => 'Buah Lokal', 'is_seasonal' => true],
        ];

        foreach ($data as $row) {
            Produk::updateOrCreate(
                ['nama' => $row['nama']],
                [
                    'satuan' => $row['satuan'],
                    'kategori_id' => $kat[$row['kategori']],
                    'is_seasonal' => $row['is_seasonal'] ?? false,
                    'is_available' => true,
                ]
            );
        }
    }
}
