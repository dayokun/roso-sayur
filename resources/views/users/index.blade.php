<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pengguna</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('sukses'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('sukses') }}</div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">{{ session('error') }}</div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-4 border-b font-semibold flex items-center">
                    Akun Dashboard
                    <a href="{{ route('users.create') }}" class="ms-auto bg-green-700 text-white px-4 py-2 rounded text-sm">Tambah Akun</a>
                </div>
                <table class="datatable w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2 text-left">Nama</th>
                            <th class="p-2 text-left">Email</th>
                            <th class="p-2 text-left">Role</th>
                            <th class="p-2 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $u)
                            <tr class="border-t">
                                <td class="p-2">{{ $u->name }}</td>
                                <td class="p-2">{{ $u->email }}</td>
                                <td class="p-2">{{ $u->role === 'admin_it' ? 'Admin IT' : 'Admin Roso Sayur' }}</td>
                                <td class="p-2 whitespace-nowrap">
                                    <a href="{{ route('users.edit', $u) }}" class="text-blue-600 underline">Edit</a>
                                    <form method="POST" action="{{ route('users.destroy', $u) }}" class="inline" onsubmit="return confirm('Hapus akun?')">@csrf @method('DELETE')<button class="text-red-600 underline ms-2">Hapus</button></form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
