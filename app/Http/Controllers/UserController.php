<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

/**
 * Kelola akun dashboard. Khusus Admin IT (middleware role).
 */
class UserController extends Controller
{
    public function __construct(protected UserService $service = new UserService()) {}

    public function index()
    {
        return view('users.index', ['users' => $this->service->daftar()]);
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(StoreUserRequest $request)
    {
        $this->service->simpan($request->validated());

        return redirect()->route('users.index')->with('sukses', 'Akun dibuat.');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->service->ubah($user, $request->validated());

        return redirect()->route('users.index')->with('sukses', 'Akun diperbarui.');
    }

    public function destroy(Request $request, User $user)
    {
        try {
            $this->service->hapus($user, $request->user());
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('sukses', 'Akun dihapus.');
    }
}
