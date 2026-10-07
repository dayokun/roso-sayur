<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;

/**
 * Dashboard operasional harian (thin controller).
 */
class DashboardController extends Controller
{
    public function __construct(protected DashboardService $service = new DashboardService()) {}

    public function index()
    {
        return view('dashboard', ['ringkasan' => $this->service->ringkasan()]);
    }
}
