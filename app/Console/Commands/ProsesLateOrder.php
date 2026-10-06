<?php

namespace App\Console\Commands;

use App\Services\LateOrderService;
use Illuminate\Console\Command;

class ProsesLateOrder extends Command
{
    protected $signature = 'late-order:proses';

    protected $description = 'Reminder & auto-tolak late order yang melewati SLA (PRD 6.4)';

    public function handle(LateOrderService $service): int
    {
        $hasil = $service->prosesOtomatis();
        $this->info("Reminder: {$hasil['reminder']}, auto-tolak: {$hasil['ditolak']}");

        return self::SUCCESS;
    }
}
