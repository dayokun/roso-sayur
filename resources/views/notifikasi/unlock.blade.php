<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Unlock Notifikasi — {{ $tgl }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                @if (session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
                @endif
                <p class="text-sm text-gray-600 mb-4">
                    Hanya <strong>Admin IT</strong> yang dapat unlock. Maksimal <strong>2x per hari</strong>.
                    Alasan unlock wajib diisi dan tercatat permanen di audit trail.
                </p>
                <form method="POST" action="{{ route('notifikasi.unlock.store') }}">
                    @csrf
                    <input type="hidden" name="tgl" value="{{ $tgl }}" />
                    <div class="mb-4">
                        <label class="text-sm text-gray-600">Alasan unlock (min. 10 karakter)</label>
                        <textarea name="alasan" rows="4" class="block w-full border rounded px-2 py-1" required>{{ old('alasan') }}</textarea>
                        @error('alasan')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                    </div>
                    <button class="bg-amber-600 text-white px-4 py-2 rounded">Unlock</button>
                    <a href="{{ route('notifikasi.index', ['tgl' => $tgl]) }}" class="ms-3 text-gray-600 underline">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
