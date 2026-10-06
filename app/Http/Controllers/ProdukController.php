<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKategoriRequest;
use App\Http\Requests\StoreProdukRequest;
use App\Http\Requests\UpdateKategoriRequest;
use App\Http\Requests\UpdateProdukRequest;
use App\Models\KategoriProduk;
use App\Models\Produk;
use App\Services\ProdukService;

/**
 * Master data produk & kategori + safety buffer (thin controller).
 */
class ProdukController extends Controller
{
    public function __construct(protected ProdukService $service = new ProdukService()) {}

    public function index()
    {
        return view('produk.index', [
            'produks' => $this->service->daftarProduk(),
            'kategoris' => $this->service->daftarKategori(),
        ]);
    }

    public function create()
    {
        return view('produk.create', ['kategoris' => $this->service->daftarKategori()]);
    }

    public function store(StoreProdukRequest $request)
    {
        $this->service->simpanProduk($request->validated());

        return redirect()->route('produk.index')->with('sukses', 'Produk ditambahkan.');
    }

    public function edit(Produk $produk)
    {
        return view('produk.edit', [
            'produk' => $produk,
            'kategoris' => $this->service->daftarKategori(),
        ]);
    }

    public function update(UpdateProdukRequest $request, Produk $produk)
    {
        $this->service->ubahProduk($produk, $request->validated());

        return redirect()->route('produk.index')->with('sukses', 'Produk diperbarui.');
    }

    public function destroy(Produk $produk)
    {
        try {
            $this->service->hapusProduk($produk);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('sukses', 'Produk dihapus.');
    }

    public function storeKategori(StoreKategoriRequest $request)
    {
        $this->service->simpanKategori($request->validated());

        return back()->with('sukses', 'Kategori ditambahkan.');
    }

    public function updateKategori(UpdateKategoriRequest $request, KategoriProduk $kategori)
    {
        $this->service->ubahKategori($kategori, $request->validated());

        return back()->with('sukses', 'Kategori diperbarui.');
    }
}
