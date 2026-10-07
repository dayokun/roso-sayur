<div class="min-h-screen flex items-center justify-center bg-gray-50 px-4 py-12">
    <div class="w-full max-w-md">
        <a href="/" class="flex items-center justify-center gap-2 mb-8">
            <span class="w-10 h-10 rounded-xl bg-green-600 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 3c3.5 0 8.5 1.5 11 6.5C18.5 14.5 16 20 12 21c.5-4 .5-8-1.5-11C8 7 5 5 5 3z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21c-4 0-8-3-9-8"/>
                </svg>
            </span>
            <span class="font-extrabold text-2xl text-green-800 tracking-tight">Roso Sayur</span>
        </a>
        <div class="bg-white rounded-3xl shadow-xl shadow-green-900/5 border border-gray-100 p-8 sm:p-10">
            {{ $slot }}
        </div>
        <p class="mt-6 text-center text-xs text-gray-400">
            <a href="{{ route('login') }}" class="text-green-700 hover:text-green-800 font-medium">Kembali ke halaman masuk</a>
        </p>
    </div>
</div>
