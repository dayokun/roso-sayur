<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Akun</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        @foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                    </div>
                @endif
                <form method="POST" action="{{ route('users.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="text-sm text-gray-600">Nama</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="block w-full border rounded px-2 py-1" required />
                    </div>
                    <div class="mb-4">
                        <label class="text-sm text-gray-600">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="block w-full border rounded px-2 py-1" required />
                    </div>
                    <div class="mb-4">
                        <label class="text-sm text-gray-600">Password (min. 8 karakter)</label>
                        <input type="password" name="password" class="block w-full border rounded px-2 py-1" required />
                    </div>
                    <div class="mb-4">
                        <label class="text-sm text-gray-600">Konfirmasi password</label>
                        <input type="password" name="password_confirmation" class="block w-full border rounded px-2 py-1" required />
                    </div>
                    <div class="mb-4">
                        <label class="text-sm text-gray-600">Role</label>
                        <select name="role" class="block w-full border rounded px-2 py-1" required>
                            <option value="admin_roso">Admin Roso Sayur (operasional)</option>
                            <option value="admin_it">Admin IT (super admin)</option>
                        </select>
                    </div>
                    <button class="bg-green-700 text-white px-4 py-2 rounded">Simpan</button>
                    <a href="{{ route('users.index') }}" class="ms-3 text-gray-600 underline">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
