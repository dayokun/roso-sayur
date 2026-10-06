<?php

namespace Database\Seeders;

use App\Models\KategoriProduk;
use Illuminate\Database\Seeder;

class KategoriProdukSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nama_kategori' => 'Sayur Daun', 'safety_buffer_persen' => 12.00],
            ['nama_kategori' => 'Sayur Umbi & Buah', 'safety_buffer_persen' => 8.00],
            ['nama_kategori' => 'Buah Lokal', 'safety_buffer_persen' => 10.00],
        ];

        foreach ($data as $row) {
            KategoriProduk::updateOrCreate(['nama_kategori' => $row['nama_kategori']], $row);
        }
    }
}
