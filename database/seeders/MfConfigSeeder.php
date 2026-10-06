<?php

namespace Database\Seeders;

use App\Models\Produk;
use App\Services\MfConfigService;
use Illuminate\Database\Seeder;

class MfConfigSeeder extends Seeder
{
    public function run(): void
    {
        $service = new MfConfigService();
        $ok = 0;
        $skip = 0;

        foreach (Produk::where('is_available', true)->where('is_seasonal', false)->get() as $produk) {
            if ($service->generate($produk)) {
                $ok++;
            } else {
                $skip++;
            }
        }

        $this->command->info("MF config: {$ok} produk OK, {$skip} dilewati (histori < 30 hari)");
    }
}
