<?php

namespace App\Http\Controllers;

use App\Services\KonsumenService;

/**
 * Daftar konsumen (terdaftar otomatis via bot WhatsApp).
 */
class KonsumenController extends Controller
{
    public function __construct(protected KonsumenService $service = new KonsumenService()) {}

    public function index()
    {
        return view('konsumen.index', ['konsumens' => $this->service->daftar()]);
    }
}
