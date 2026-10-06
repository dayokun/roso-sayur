<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

/**
 * Manajemen akun dashboard. Hanya untuk Admin IT (dijaga middleware + Form Request).
 */
class UserService
{
    public function daftar(): Collection
    {
        return User::orderBy('name')->get();
    }

    public function simpan(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);
    }

    public function ubah(User $user, array $data): User
    {
        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
        ];
        if (! empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }
        $user->update($payload);

        return $user->fresh();
    }

    public function hapus(User $user, User $aktor): void
    {
        if ($user->id === $aktor->id) {
            throw new \RuntimeException('Tidak bisa menghapus akun sendiri.');
        }
        $user->delete();
    }
}
