<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Roso Sayur') }}</title>

        <!-- Fonts: Plus Jakarta Sans -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- DataTables -->
        <!-- DataTables diganti RsTable vanilla (tanpa CDN) -->
        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>

        <style>
            :root {
                --rs-bg: #f2f5f1;
                --rs-surface: #ffffff;
                --rs-surface-2: #f7f9f6;
                --rs-ink: #152019;
                --rs-muted: #68736c;
                --rs-line: #dde4de;
                --rs-green: #16794a;
                --rs-green-2: #20a365;
                --rs-green-soft: #e2f4e9;
                --rs-dark: #12251b;
                --rs-lime: #c8f06a;
                --rs-amber: #aa6511;
                --rs-amber-soft: #fff0d6;
                --rs-red: #b33e45;
                --rs-red-soft: #fde7e7;
                --rs-blue: #246a9a;
                --rs-blue-soft: #e4f1fb;
                --rs-shadow: 0 10px 30px rgba(26, 53, 36, .07);
                --rs-radius: 20px;
            }
            [x-cloak] { display: none !important; }
            body { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }

            /* Scrollbar sidebar */
            .nice-scroll::-webkit-scrollbar { width: 6px; }
            .nice-scroll::-webkit-scrollbar-track { background: transparent; }
            .nice-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,.18); border-radius: 999px; }
            .nice-scroll::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,.32); }

            /* Animasi masuk konten */
            @keyframes pageIn {
                from { opacity: 0; transform: translateY(12px); }
                to   { opacity: 1; transform: translateY(0); }
            }
            .page-enter { animation: pageIn .45s cubic-bezier(.21,1.02,.73,1) both; }

            /* ===== Kartu metrik ala mockup ===== */
            .rs-metric {
                background: var(--rs-surface);
                border: 1px solid var(--rs-line);
                border-radius: var(--rs-radius);
                box-shadow: var(--rs-shadow);
                padding: 1.125rem 1.25rem;
                position: relative;
                overflow: hidden;
                transition: transform .18s ease, box-shadow .18s ease;
            }
            .rs-metric:hover { transform: translateY(-2px); box-shadow: 0 14px 34px rgba(26,53,36,.11); }
            .rs-metric::after {
                content: ""; position: absolute; width: 68px; height: 68px;
                border-radius: 50%; background: var(--rs-tone, var(--rs-green-soft));
                right: -20px; top: -20px;
            }
            .rs-metric .rs-label { font-size: .75rem; color: var(--rs-muted); font-weight: 600; }
            .rs-metric .rs-value { font-size: 1.625rem; font-weight: 800; margin: .55rem 0 .35rem; letter-spacing: -.5px; color: var(--rs-ink); }
            .rs-metric .rs-delta { font-size: .7rem; color: var(--rs-green); font-weight: 700; }
            .rs-metric .rs-delta.neutral { color: var(--rs-muted); }
            .rs-metric .rs-delta.warn { color: var(--rs-amber); }
            .rs-metric .rs-delta.bad { color: var(--rs-red); }

            /* ===== Kartu umum ===== */
            .rs-card {
                background: var(--rs-surface);
                border: 1px solid var(--rs-line);
                border-radius: var(--rs-radius);
                box-shadow: var(--rs-shadow);
            }

            /* ===== Badge status ===== */
            .rs-badge {
                display: inline-flex; align-items: center; gap: .3rem;
                padding: .3rem .65rem; border-radius: 999px;
                font-size: .68rem; font-weight: 800; white-space: nowrap;
            }
            .rs-badge.pending   { background: var(--rs-amber-soft); color: var(--rs-amber); }
            .rs-badge.diterima  { background: var(--rs-blue-soft); color: var(--rs-blue); }
            .rs-badge.siap_diambil { background: var(--rs-green-soft); color: var(--rs-green); }
            .rs-badge.selesai   { background: #edf0ee; color: #536159; }
            .rs-badge.cancelled { background: var(--rs-red-soft); color: var(--rs-red); }
            .rs-badge.info      { background: var(--rs-blue-soft); color: var(--rs-blue); }
            .rs-badge.method    { background: #eef2ff; color: #4338ca; }

            /* ===== Tombol ===== */
            .rs-btn {
                display: inline-flex; align-items: center; justify-content: center; gap: .5rem;
                border-radius: 13px; padding: .68rem 1rem;
                font-weight: 700; font-size: .78rem; cursor: pointer;
                transition: transform .12s ease, box-shadow .15s ease, background .15s ease;
                border: 0;
            }
            .rs-btn:active { transform: translateY(1px) scale(.99); }
            .rs-btn-primary { background: var(--rs-green); color: #fff; box-shadow: 0 4px 14px rgba(22,121,74,.28); }
            .rs-btn-primary:hover { background: #126b41; box-shadow: 0 6px 18px rgba(22,121,74,.36); }
            .rs-btn-secondary { background: var(--rs-surface); color: var(--rs-ink); border: 1px solid var(--rs-line); }
            .rs-btn-secondary:hover { border-color: var(--rs-green-2); color: var(--rs-green); }
            .rs-btn-dark { background: var(--rs-dark); color: #fff; }
            .rs-btn-dark:hover { background: #1b3326; }
            .rs-btn-danger { background: var(--rs-red-soft); color: var(--rs-red); }
            .rs-btn-danger:hover { background: #fbd5d5; }
            .rs-btn-amber { background: #b45309; color: #fff; }
            .rs-btn-amber:hover { background: #92400e; }

            /* ===== Input ===== */
            .rs-input, .rs-select, .rs-textarea {
                width: 100%; background: var(--rs-surface-2);
                border: 1px solid var(--rs-line); color: var(--rs-ink);
                border-radius: 12px; padding: .65rem .8rem;
                font-size: .82rem; font-weight: 500; outline: none;
                transition: border-color .15s, box-shadow .15s;
            }
            .rs-input:focus, .rs-select:focus, .rs-textarea:focus {
                border-color: var(--rs-green-2);
                box-shadow: 0 0 0 3px rgba(32,163,101,.15);
            }
            .rs-label { font-size: .68rem; color: var(--rs-muted); font-weight: 700; display: block; margin-bottom: .35rem; }

            /* ===== Toast ===== */
            #rs-toast {
                position: fixed; right: 24px; bottom: 24px; z-index: 100;
                background: var(--rs-dark); color: #fff;
                border-radius: 14px; padding: .85rem 1.1rem;
                font-size: .78rem; font-weight: 700; max-width: 360px;
                box-shadow: 0 12px 32px rgba(0,0,0,.22);
                opacity: 0; transform: translateY(14px); pointer-events: none;
                transition: opacity .22s ease, transform .22s ease;
                display: flex; align-items: center; gap: .6rem;
            }
            #rs-toast.show { opacity: 1; transform: none; }
            #rs-toast.success { border-left: 4px solid var(--rs-green-2); }
            #rs-toast.error { border-left: 4px solid #f18489; }
            @media (max-width: 640px) { #rs-toast { left: 14px; right: 14px; bottom: 82px; } }

            /* ===== Score ring (laporan) ===== */
            .rs-score-ring {
                width: 74px; height: 74px; border-radius: 50%;
                background: conic-gradient(var(--rs-green) var(--rs-score, 50%), var(--rs-line) 0);
                display: grid; place-items: center; position: relative; flex-shrink: 0;
            }
            .rs-score-ring::before {
                content: ""; position: absolute; inset: 8px; border-radius: 50%; background: var(--rs-surface);
            }
            .rs-score-ring b { position: relative; font-size: .85rem; }

            /* ===== DataTables: tema Roso Sayur ===== */
            .dt-container { font-size: .875rem; }
            .dt-layout-table { overflow-x: auto; border-radius: .75rem; }
            .dt-layout-row { display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap; margin: .9rem 0; }
            .dt-search label, .dt-length label { display: flex; align-items: center; gap: .5rem; color: var(--rs-muted); font-size: .875rem; font-weight: 600; }
            .dt-search input, .dt-length select {
                border: 1px solid var(--rs-line); border-radius: .75rem; padding: .5rem .75rem;
                font-size: .875rem; background: var(--rs-surface-2); color: var(--rs-ink);
                transition: border-color .15s, box-shadow .15s;
            }
            .dt-search input:focus, .dt-length select:focus {
                outline: none; border-color: var(--rs-green-2); box-shadow: 0 0 0 3px rgba(32,163,101,.15);
            }
            .dt-info { color: var(--rs-muted); font-size: .8rem; }
            .dt-paging nav { display: flex; gap: .25rem; }
            .dt-paging .dt-paging-button {
                min-width: 2.25rem; height: 2.25rem; padding: 0 .55rem; margin: 0;
                display: inline-flex; align-items: center; justify-content: center;
                border-radius: .75rem; border: 1px solid var(--rs-line); background: var(--rs-surface);
                color: var(--rs-ink); font-size: .875rem; cursor: pointer; transition: all .15s;
            }
            .dt-paging .dt-paging-button:hover:not(.disabled):not(.current) { border-color: var(--rs-green-2); color: var(--rs-green); transform: translateY(-1px); }
            .dt-paging .dt-paging-button.current { background: var(--rs-green); border-color: var(--rs-green); color: #fff; font-weight: 700; box-shadow: 0 2px 6px rgba(22,121,74,.35); }
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
            table.datatable thead .dt-orderable-desc .dt-column-order::after { color: var(--rs-green); }
        
            /* ===== RsTable: search + length + pagination (vanilla) ===== */
            .rs-table-toolbar { display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap; margin: .9rem 0; }
            .rs-table-length, .rs-table-search { display: flex; align-items: center; gap: .5rem; color: #4b5563; font-size: .875rem; }
            .rs-table-search input, .rs-table-length select {
                border: 1px solid #d1d5db; border-radius: .75rem; padding: .5rem .75rem;
                font-size: .875rem; background: #fff; transition: border-color .15s, box-shadow .15s;
            }
            .rs-table-search input:focus, .rs-table-length select:focus {
                outline: none; border-color: #16a34a; box-shadow: 0 0 0 3px rgba(22,163,74,.15);
            }
            .rs-table-scroll { overflow-x: auto; border-radius: .75rem; }
            .rs-table-footer { display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap; margin: .9rem 0 .2rem; }
            .rs-table-info { color: #6b7280; font-size: .8rem; }
            .rs-table-paging { display: flex; gap: .25rem; flex-wrap: wrap; }
            .rs-page-btn {
                min-width: 2.25rem; height: 2.25rem; padding: 0 .55rem;
                display: inline-flex; align-items: center; justify-content: center;
                border-radius: .75rem; border: 1px solid #e5e7eb; background: #fff;
                color: #374151; font-size: .875rem; cursor: pointer; transition: all .15s;
            }
            .rs-page-btn:hover:not(.disabled):not(.active) { border-color: #16a34a; color: #15803d; transform: translateY(-1px); }
            .rs-page-btn.active { background: #16a34a; border-color: #16a34a; color: #fff; font-weight: 700; box-shadow: 0 2px 6px rgba(22,163,74,.35); }
            .rs-page-btn.disabled { opacity: .35; cursor: default; }
            @media (max-width: 640px) {
                .rs-table-toolbar { flex-direction: column; align-items: stretch; }
                .rs-table-search input { flex: 1; }
            }

        </style>
    </head>
    <body class="antialiased" style="background: var(--rs-bg); color: var(--rs-ink);">
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
                <header class="sticky top-0 z-20 backdrop-blur-md border-b" style="background: color-mix(in srgb, var(--rs-bg) 88%, transparent); border-color: var(--rs-line);">
                    <div class="flex items-center justify-between h-16 px-4 sm:px-6">
                        <div class="flex items-center gap-2 min-w-0">
                            <button @click="sidebarOpen = true"
                                    class="lg:hidden p-2 -ml-2 rounded-xl hover:bg-black/5 active:scale-95 transition" style="color: var(--rs-muted);">
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
                            <span class="hidden md:flex items-center gap-2 text-sm" style="color: var(--rs-muted);">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ now()->translatedFormat('l, d F Y') }}
                            </span>
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="flex items-center gap-2.5 pl-1.5 pr-2.5 py-1.5 rounded-full hover:bg-black/5 active:scale-95 transition">
                                        <span class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-extrabold shadow" style="background: var(--rs-lime); color: #18391f;">
                                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                        </span>
                                        <span class="hidden sm:block text-left">
                                            <span class="block text-sm font-bold leading-tight" style="color: var(--rs-ink);">{{ Auth::user()->name }}</span>
                                            <span class="block text-xs leading-tight" style="color: var(--rs-muted);">{{ Auth::user()->isAdminIt() ? 'Admin IT' : 'Admin Roso Sayur' }}</span>
                                        </span>
                                        <svg class="w-4 h-4" style="color: var(--rs-muted);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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

                <footer class="px-6 py-4 text-center text-xs" style="color: var(--rs-muted);">
                    Roso Sayur &copy; {{ date('Y') }} — Sistem Prediksi Adaptif Kebutuhan Stok
                </footer>
            </div>
        </div>

        <!-- Toast notification -->
        <div id="rs-toast" role="status" aria-live="polite">
            <svg id="rs-toast-icon" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span id="rs-toast-msg"></span>
        </div>

        <!-- DataTables -->
        <!-- DataTables JS dihapus, pakai RsTable vanilla -->
        <script>
            // Toast global ala mockup: slide-in kanan bawah, auto-dismiss 2.5 detik
            window.rsToast = function(msg, type = 'success') {
                const t = document.getElementById('rs-toast');
                const m = document.getElementById('rs-toast-msg');
                m.textContent = msg;
                t.className = 'show ' + type;
                clearTimeout(window._rsToastTimer);
                window._rsToastTimer = setTimeout(() => t.classList.remove('show'), 2500);
            };

            document.addEventListener('DOMContentLoaded', () => {
                // Ubah session flash Laravel menjadi toast
                @if (session('sukses'))
                    rsToast(@json(session('sukses')), 'success');
                @endif
                @if (session('error'))
                    rsToast(@json(session('error')), 'error');
                @endif

                // RsTable: pencarian + pilihan jumlah data + pagination (vanilla JS, tanpa CDN)
            document.querySelectorAll('table.datatable').forEach((table) => {
                if (table.dataset.rsEnhanced) return;
                table.dataset.rsEnhanced = '1';

                const tbody = table.querySelector('tbody');
                const allRows = Array.from(tbody.querySelectorAll('tr'));
                // Abaikan baris "empty" (colspan)
                const dataRows = allRows.filter(r => r.querySelectorAll('td').length > 1 || !r.querySelector('td[colspan]'));
                const emptyRow = allRows.find(r => r.querySelector('td[colspan]'));

                let pageLength = 10, currentPage = 1, query = '';

                // Bungkus tabel
                const wrap = document.createElement('div');
                wrap.className = 'rs-table-wrap';
                table.parentNode.insertBefore(wrap, table);

                // Toolbar: search + length
                const toolbar = document.createElement('div');
                toolbar.className = 'rs-table-toolbar';
                toolbar.innerHTML = `
                    <label class="rs-table-length">Tampilkan
                        <select>
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select> data
                    </label>
                    <label class="rs-table-search">Cari:
                        <input type="search" placeholder="Ketik kata kunci…">
                    </label>`;
                wrap.appendChild(toolbar);

                const scrollDiv = document.createElement('div');
                scrollDiv.className = 'rs-table-scroll';
                scrollDiv.appendChild(table);
                wrap.appendChild(scrollDiv);

                const footer = document.createElement('div');
                footer.className = 'rs-table-footer';
                footer.innerHTML = `<span class="rs-table-info"></span><nav class="rs-table-paging"></nav>`;
                wrap.appendChild(footer);

                const info = footer.querySelector('.rs-table-info');
                const paging = footer.querySelector('.rs-table-paging');
                const searchInput = toolbar.querySelector('input');
                const lengthSelect = toolbar.querySelector('select');

                function filtered() {
                    if (!query) return dataRows;
                    const q = query.toLowerCase();
                    return dataRows.filter(r => r.textContent.toLowerCase().includes(q));
                }

                function render() {
                    const rows = filtered();
                    const total = rows.length;
                    const pages = Math.max(1, Math.ceil(total / pageLength));
                    if (currentPage > pages) currentPage = pages;
                    const start = (currentPage - 1) * pageLength;
                    const end = Math.min(start + pageLength, total);

                    dataRows.forEach(r => r.style.display = 'none');
                    rows.slice(start, end).forEach(r => r.style.display = '');
                    if (emptyRow) emptyRow.style.display = total ? 'none' : '';

                    info.textContent = total
                        ? `Menampilkan ${start + 1}–${end} dari ${total} data`
                        : 'Tidak ada data';

                    // Pagination buttons
                    paging.innerHTML = '';
                    const btn = (label, page, disabled, active) => {
                        const b = document.createElement('button');
                        b.type = 'button'; b.textContent = label;
                        b.className = 'rs-page-btn' + (active ? ' active' : '') + (disabled ? ' disabled' : '');
                        b.disabled = !!disabled;
                        b.addEventListener('click', () => { currentPage = page; render(); });
                        paging.appendChild(b);
                    };
                    btn('«', 1, currentPage === 1);
                    btn('‹', currentPage - 1, currentPage === 1);
                    // nomor halaman (maks 5)
                    let s = Math.max(1, Math.min(currentPage - 2, pages - 4));
                    let e = Math.min(pages, s + 4);
                    for (let i = s; i <= e; i++) btn(String(i), i, false, i === currentPage);
                    btn('›', currentPage + 1, currentPage === pages);
                    btn('»', pages, currentPage === pages);
                }

                searchInput.addEventListener('input', () => { query = searchInput.value.trim(); currentPage = 1; render(); });
                lengthSelect.addEventListener('change', () => { pageLength = parseInt(lengthSelect.value, 10); currentPage = 1; render(); });

                render();
            });
            });
        </script>
        @stack('scripts')
    </body>
</html>
