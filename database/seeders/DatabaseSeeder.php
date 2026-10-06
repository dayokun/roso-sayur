<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            KategoriProdukSeeder::class,
            ProdukSeeder::class,
            KonsumenSeeder::class,
            PesananSeeder::class,
            MfConfigSeeder::class,
        ]);
    }
}
