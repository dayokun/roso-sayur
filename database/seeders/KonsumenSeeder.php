<?php

namespace Database\Seeders;

use App\Models\Konsumen;
use Illuminate\Database\Seeder;

class KonsumenSeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'Budi Santoso', 'Siti Aminah', 'Agus Wijaya', 'Dewi Lestari',
            'Rina Marlina', 'Hendra Gunawan', 'Yuni Astuti', 'Tono Prasetyo',
            'Mega Wati', 'Joko Susilo', 'Fitri Handayani', 'Eko Saputra',
            'Nina Kurnia', 'Dedi Haryanto', 'Lina Marlina', 'Andi Nugraha',
            'Sari Puspita', 'Wahyu Hidayat', 'Intan Permata', 'Fajar Ramadhan',
        ];

        foreach ($names as $i => $nama) {
            Konsumen::updateOrCreate(
                ['no_hp' => '0812' . str_pad((string) (34560000 + $i * 137), 8, '0', STR_PAD_LEFT)],
                ['nama' => $nama, 'alamat' => 'Jl. Merdeka No. ' . ($i + 1) . ', Bandung']
            );
        }
    }
}
