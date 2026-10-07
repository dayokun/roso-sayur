<x-guest-layout>
    <div class="min-h-screen flex">

        <!-- Panel kiri: branding -->
        <div class="hidden lg:flex w-1/2 bg-gradient-to-br from-green-700 via-green-800 to-green-900 text-white flex-col justify-between p-12">
            <a href="/" class="flex items-center gap-3">
                <span class="w-11 h-11 rounded-2xl bg-white/15 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 3c3.5 0 8.5 1.5 11 6.5C18.5 14.5 16 20 12 21c.5-4 .5-8-1.5-11C8 7 5 5 5 3z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21c-4 0-8-3-9-8"/>
                    </svg>
                </span>
                <span class="font-extrabold text-2xl tracking-tight">Roso Sayur</span>
            </a>

            <div>
                <h1 class="text-4xl font-extrabold leading-tight tracking-tight">
                    Stok tepat,<br>segar setiap hari.
                </h1>
                <p class="mt-5 text-green-100 text-lg leading-relaxed max-w-md">
                    Dashboard operasional untuk prediksi stok, pesanan, notifikasi, dan laporan — semua dalam satu tempat.
                </p>
                <ul class="mt-8 space-y-4 text-green-50">
                    <li class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-full bg-white/15 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        Prediksi kebutuhan stok harian (Fuzzy Tsukamoto)
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-full bg-white/15 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        Pesanan masuk via bot WhatsApp
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-full bg-white/15 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        Laporan Excel &amp; PDF siap unduh
                    </li>
                </ul>
            </div>

            <p class="text-sm text-green-200/70">Sistem Prediksi Adaptif Kebutuhan Stok &copy; {{ date('Y') }}</p>
        </div>

        <!-- Panel kanan: form -->
        <div class="flex-1 flex items-center justify-center bg-gray-50 px-4 sm:px-8 py-12">
            <div class="w-full max-w-md">
                <!-- Logo mobile -->
                <a href="/" class="lg:hidden flex items-center justify-center gap-2 mb-8">
                    <span class="w-10 h-10 rounded-xl bg-green-600 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 3c3.5 0 8.5 1.5 11 6.5C18.5 14.5 16 20 12 21c.5-4 .5-8-1.5-11C8 7 5 5 5 3z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21c-4 0-8-3-9-8"/>
                        </svg>
                    </span>
                    <span class="font-extrabold text-2xl text-green-800 tracking-tight">Roso Sayur</span>
                </a>

                <div class="bg-white rounded-3xl shadow-xl shadow-green-900/5 border border-gray-100 p-8 sm:p-10">
                    <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">Selamat datang kembali</h2>
                    <p class="mt-2 text-sm text-gray-500">Masuk untuk mengelola operasional harian.</p>

                    <x-auth-session-status class="mb-4 mt-4" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}" class="mt-6">
                        @csrf

                        <div>
                            <x-input-label for="email" :value="__('Email')" class="font-semibold" />
                            <x-text-input id="email" class="block mt-2 w-full rounded-xl border-gray-200 focus:border-green-500 focus:ring-green-500" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@rososayur.test" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div class="mt-5">
                            <div class="flex items-center justify-between">
                                <x-input-label for="password" :value="__('Password')" class="font-semibold" />
                                @if (Route::has('password.request'))
                                    <a class="text-sm text-green-700 hover:text-green-800 font-medium" href="{{ route('password.request') }}">
                                        Lupa password?
                                    </a>
                                @endif
                            </div>
                            <x-text-input id="password" class="block mt-2 w-full rounded-xl border-gray-200 focus:border-green-500 focus:ring-green-500"
                                            type="password"
                                            name="password"
                                            required autocomplete="current-password"
                                            placeholder="••••••••" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div class="block mt-5">
                            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500" name="remember">
                                <span class="ms-2 text-sm text-gray-600">Ingat saya</span>
                            </label>
                        </div>

                        <button type="submit" class="mt-7 w-full py-3.5 rounded-xl bg-green-600 text-white font-bold hover:bg-green-700 transition shadow-lg shadow-green-600/25">
                            Masuk Dashboard
                        </button>
                    </form>
                </div>

                <p class="mt-6 text-center text-xs text-gray-400">
                    Area khusus staf. Pelanggan memesan via WhatsApp — tanpa perlu akun.
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>
