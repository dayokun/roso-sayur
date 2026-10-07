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

        <!-- DataTables -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/datatables.net-dt@2.3.7/css/dataTables.dataTables.min.css">

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

            /* ===== DataTables: tema Roso Sayur ===== */
            .dt-container { font-size: .875rem; }
            .dt-layout-table { overflow-x: auto; border-radius: .75rem; }
            .dt-layout-row { display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap; margin: .9rem 0; }
            .dt-search label, .dt-length label { display: flex; align-items: center; gap: .5rem; color: #4b5563; font-size: .875rem; }
            .dt-search input, .dt-length select {
                border: 1px solid #d1d5db; border-radius: .75rem; padding: .5rem .75rem;
                font-size: .875rem; background: #fff; transition: border-color .15s, box-shadow .15s;
            }
            .dt-search input:focus, .dt-length select:focus {
                outline: none; border-color: #16a34a; box-shadow: 0 0 0 3px rgba(22,163,74,.15);
            }
            .dt-info { color: #6b7280; font-size: .8rem; }
            .dt-paging nav { display: flex; gap: .25rem; }
            .dt-paging .dt-paging-button {
                min-width: 2.25rem; height: 2.25rem; padding: 0 .55rem; margin: 0;
                display: inline-flex; align-items: center; justify-content: center;
                border-radius: .75rem; border: 1px solid #e5e7eb; background: #fff;
                color: #374151; font-size: .875rem; cursor: pointer; transition: all .15s;
            }
            .dt-paging .dt-paging-button:hover:not(.disabled):not(.current) { border-color: #16a34a; color: #15803d; transform: translateY(-1px); }
            .dt-paging .dt-paging-button.current { background: #16a34a; border-color: #16a34a; color: #fff; font-weight: 700; box-shadow: 0 2px 6px rgba(22,163,74,.35); }
            .dt-paging .dt-paging-button.disabled { opacity: .35; cursor: default; }
            table.datatable { border-collapse: separate; border-spacing: 0; width: 100%; }
            table.datatable thead th {
                background: linear-gradient(to bottom, #f0fdf4, #dcfce7);
                color: #166534; font-weight: 700; text-transform: uppercase;
                font-size: .7rem; letter-spacing: .06em; padding: .8rem .75rem;
                white-space: nowrap; border-bottom: 2px solid #86efac; text-align: left;
            }
            table.datatable thead th:first-child { border-top-left-radius: .75rem; }
            table.datatable thead th:last-child { border-top-right-radius: .75rem; }
            table.datatable tbody td { padding: .7rem .75rem; border-bottom: 1px solid #f3f4f6; vertical-align: middle; }
            table.datatable tbody tr { transition: background .15s ease; }
            table.datatable tbody tr:hover { background: #f0fdf4; }
            table.datatable tbody tr:last-child td { border-bottom: none; }
            table.datatable thead .dt-orderable-asc .dt-column-order::before,
            table.datatable thead .dt-orderable-desc .dt-column-order::after { color: #16a34a; }
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
        <!-- DataTables -->
        <script src="https://cdn.jsdelivr.net/npm/datatables.net-dt@2.3.7/js/dataTables.dataTables.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('table.datatable').forEach((el) => {
                    new DataTable(el, {
                        pageLength: 10,
                        lengthMenu: [10, 25, 50, 100],
                        language: {
                            search: 'Cari:',
                            searchPlaceholder: 'Ketik kata kunci…',
                            lengthMenu: 'Tampilkan _MENU_ data',
                            info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                            infoEmpty: 'Tidak ada data',
                            infoFiltered: '(disaring dari _MAX_ total data)',
                            zeroRecords: 'Data tidak ditemukan',
                            emptyTable: 'Belum ada data',
                            paginate: { first: '«', previous: '‹', next: '›', last: '»' },
                        },
                    });
                });
            });
        </script>
        @stack('scripts')
    </body>
</html>
