<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Roso Sayur') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] { display: none !important; }
            /* Scrollbar sidebar */
            .nice-scroll::-webkit-scrollbar { width: 6px; }
            .nice-scroll::-webkit-scrollbar-track { background: transparent; }
            .nice-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,.18); border-radius: 999px; }
            .nice-scroll::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,.32); }
            /* Animasi masuk konten */
            @keyframes pageIn {
                from { opacity: 0; transform: translateY(10px); }
                to   { opacity: 1; transform: translateY(0); }
            }
            .page-enter { animation: pageIn .45s cubic-bezier(.21,1.02,.73,1) both; }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-100 text-gray-900">
        <div x-data="{
                sidebarOpen: false,
                collapsed: localStorage.getItem('rs-sidebar') === 'collapsed',
                toggleCollapse() {
                    this.collapsed = !this.collapsed;
                    localStorage.setItem('rs-sidebar', this.collapsed ? 'collapsed' : 'expanded');
                }
             }"
             x-cloak>

            @include('layouts.sidebar')

            <!-- Backdrop (mobile) -->
            <div x-show="sidebarOpen"
                 x-transition:enter="transition-opacity ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="sidebarOpen = false"
                 class="fixed inset-0 z-30 bg-gray-900/50 backdrop-blur-[2px] lg:hidden"></div>

            <!-- Kolom utama -->
            <div :class="collapsed ? 'lg:pl-20' : 'lg:pl-64'" class="min-h-screen flex flex-col transition-[padding] duration-300">

                <!-- Topbar -->
                <header class="sticky top-0 z-20 bg-white/85 backdrop-blur-md border-b border-gray-200/80">
                    <div class="flex items-center justify-between h-16 px-4 sm:px-6">
                        <div class="flex items-center gap-2 min-w-0">
                            <button @click="sidebarOpen = true"
                                    class="lg:hidden p-2 -ml-2 rounded-xl text-gray-500 hover:bg-gray-100 hover:text-gray-700 active:scale-95 transition">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                            </button>
                            <div class="min-w-0 truncate">
                                @isset($header)
                                    {{ $header }}
                                @endisset
                            </div>
                        </div>
                        <div class="flex items-center gap-3 sm:gap-5">
                            <span class="hidden md:flex items-center gap-2 text-sm text-gray-500">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ now()->translatedFormat('d M Y') }}
                            </span>
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="flex items-center gap-2.5 pl-1.5 pr-2.5 py-1.5 rounded-full hover:bg-gray-100 active:scale-95 transition">
                                        <span class="w-9 h-9 rounded-full bg-gradient-to-br from-green-500 to-green-700 flex items-center justify-center text-white font-bold text-sm shadow">
                                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                        </span>
                                        <span class="hidden sm:block text-left">
                                            <span class="block text-sm font-semibold text-gray-800 leading-tight">{{ Auth::user()->name }}</span>
                                            <span class="block text-xs text-gray-500 leading-tight">{{ Auth::user()->isAdminIt() ? 'Admin IT' : 'Admin Roso Sayur' }}</span>
                                        </span>
                                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('profile.edit')">
                                        Profil Saya
                                    </x-dropdown-link>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')"
                                                onclick="event.preventDefault(); this.closest('form').submit();"
                                                class="text-red-600 hover:text-red-700">
                                            Keluar
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    </div>
                </header>

                <!-- Konten -->
                <main class="flex-1 w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-6">
                    <div class="page-enter">
                        {{ $slot }}
                    </div>
                </main>

                <footer class="px-6 py-4 text-center text-xs text-gray-400">
                    Roso Sayur &copy; {{ date('Y') }} — Sistem Prediksi Adaptif Kebutuhan Stok
                </footer>
            </div>
        </div>
        @stack('scripts')
    </body>
</html>
