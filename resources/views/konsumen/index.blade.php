<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Konsumen</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2 text-left">Nama</th>
                            <th class="p-2 text-left">No HP</th>
                            <th class="p-2 text-left">Jumlah Pesanan</th>
                            <th class="p-2 text-left">Terdaftar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($konsumens as $k)
                            <tr class="border-t">
                                <td class="p-2">{{ $k->nama }}</td>
                                <td class="p-2">{{ $k->no_hp }}</td>
                                <td class="p-2">{{ $k->pesanans_count }}</td>
                                <td class="p-2">{{ $k->created_at->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="p-4 text-center text-gray-500">Belum ada konsumen.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4">{{ $konsumens->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
