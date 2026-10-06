<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Log Unlock Notifikasi</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2 text-left">Waktu Unlock</th>
                            <th class="p-2 text-left">Tanggal Notifikasi</th>
                            <th class="p-2 text-left">Admin</th>
                            <th class="p-2 text-left">Alasan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                            <tr class="border-t">
                                <td class="p-2">{{ $log->timestamp_unlock->format('d/m/Y H:i') }}</td>
                                <td class="p-2">{{ $log->tgl->format('d/m/Y') }}</td>
                                <td class="p-2">{{ $log->admin->name }}</td>
                                <td class="p-2">{{ $log->alasan }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="p-4 text-center text-gray-500">Belum ada riwayat unlock.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4">{{ $logs->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
