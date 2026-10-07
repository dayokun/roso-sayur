@php
$icons = [
    'dashboard' => '<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>',
    'pesanan'   => '<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>',
    'pembelian' => '<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>',
    'prediksi'  => '<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>',
    'notifikasi'=> '<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>',
    'sisa-stok' => '<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v12a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>',
    'produk'    => '<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>',
    'konsumen'  => '<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>',
    'laporan'   => '<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
    'pengguna'  => '<svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>',
];

$lateCount = \App\Models\Pesanan::where('late_order_status', 'pending_approval')->count();

$groups = [
    ['label' => 'Navigasi', 'items' => [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'match' => 'dashboard', 'icon' => 'dashboard'],
    ]],
    ['label' => 'Operasional', 'items' => [
        ['label' => 'Pesanan', 'route' => 'pesanan.index', 'match' => 'pesanan.*', 'icon' => 'pesanan', 'badge' => $lateCount],
        ['label' => 'Pembelian', 'route' => 'pembelian.index', 'match' => 'pembelian.*', 'icon' => 'pembelian'],
        ['label' => 'Prediksi', 'route' => 'prediksi.index', 'match' => 'prediksi.*', 'icon' => 'prediksi'],
        ['label' => 'Notifikasi', 'route' => 'notifikasi.index', 'match' => 'notifikasi.*', 'icon' => 'notifikasi'],
        ['label' => 'Sisa Stok', 'route' => 'sisa-stok.index', 'match' => 'sisa-stok.*', 'icon' => 'sisa-stok'],
    ]],
    ['label' => 'Data Master', 'items' => [
        ['label' => 'Produk', 'route' => 'produk.index', 'match' => 'produk.*', 'icon' => 'produk'],
        ['label' => 'Konsumen', 'route' => 'konsumen.index', 'match' => 'konsumen.*', 'icon' => 'konsumen'],
    ]],
    ['label' => 'Laporan', 'items' => [
        ['label' => 'Laporan', 'route' => 'laporan.index', 'match' => 'laporan.*', 'icon' => 'laporan'],
    ]],
];

if (auth()->user()?->isAdminIt()) {
    $groups[] = ['label' => 'Sistem', 'items' => [
        ['label' => 'Pengguna', 'route' => 'users.index', 'match' => 'users.*', 'icon' => 'pengguna'],
    ]];
}
@endphp

<aside
    :class="{
        'translate-x-0': sidebarOpen,
        '-translate-x-full': !sidebarOpen,
        'lg:w-20': collapsed,
        'lg:w-64': !collapsed
    }"
    class="fixed inset-y-0 left-0 z-40 flex flex-col w-64 lg:translate-x-0 transition-all duration-300 bg-gradient-to-b from-green-900 via-green-900 to-[#0c3b21] text-green-50 shadow-2xl"
>
    <!-- Logo -->
    <div class="flex items-center gap-3 px-5 h-16 shrink-0 border-b border-white/10">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 min-w-0">
            <span class="w-10 h-10 shrink-0 rounded-xl bg-white/15 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 3c3.5 0 8.5 1.5 11 6.5C18.5 14.5 16 20 12 21c.5-4 .5-8-1.5-11C8 7 5 5 5 3z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21c-4 0-8-3-9-8"/>
                </svg>
            </span>
            <span class="font-extrabold text-lg tracking-tight whitespace-nowrap overflow-hidden" :class="collapsed && 'lg:hidden'">Roso Sayur</span>
        </a>
        <button @click="toggleCollapse()" title="Ciutkan menu"
                class="hidden lg:flex ml-auto w-8 h-8 shrink-0 rounded-lg items-center justify-center text-green-200/70 hover:bg-white/10 hover:text-white transition">
            <svg class="w-5 h-5 transition-transform duration-300" :class="collapsed && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>
    </div>

    <!-- Menu -->
    <nav class="flex-1 overflow-y-auto nice-scroll px-3 py-4">
        @foreach($groups as $gi => $group)
            <p class="px-3 {{ $gi > 0 ? 'pt-6' : 'pt-1' }} pb-2 text-[11px] font-bold uppercase tracking-[0.15em] text-green-200/50 whitespace-nowrap overflow-hidden"
               :class="collapsed && 'lg:hidden'">{{ $group['label'] }}</p>
            <div class="space-y-1">
                @foreach($group['items'] as $item)
                    @php $active = request()->routeIs($item['match']); @endphp
                    <a href="{{ route($item['route']) }}" title="{{ $item['label'] }}"
                       @click="if (window.innerWidth < 1024) sidebarOpen = false"
                       class="group relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                              {{ $active
                                  ? 'bg-white/15 text-white shadow-[inset_0_1px_0_rgba(255,255,255,0.1)]'
                                  : 'text-green-100/75 hover:bg-white/10 hover:text-white hover:translate-x-1' }}">
                        @if($active)
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-7 bg-green-300 rounded-r-full shadow-[0_0_8px_rgba(134,239,172,0.8)]"></span>
                        @endif
                        <span class="shrink-0 {{ $active ? 'text-green-300' : 'text-green-200/60 group-hover:text-green-200 group-hover:scale-110' }} transition-all duration-200">{!! $icons[$item['icon']] !!}</span>
                        <span class="flex-1 whitespace-nowrap overflow-hidden" :class="collapsed && 'lg:hidden'">{{ $item['label'] }}</span>
                        @if(!empty($item['badge']))
                            <span class="min-w-[1.5rem] h-6 px-1.5 inline-flex items-center justify-center rounded-full bg-amber-400 text-amber-950 text-xs font-bold shadow"
                                  :class="collapsed && 'lg:hidden'" title="Late order menunggu persetujuan">{{ $item['badge'] }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        @endforeach
    </nav>

    <!-- User -->
    <div class="p-4 shrink-0 border-t border-white/10">
        <div class="flex items-center gap-3 rounded-xl bg-white/5 p-2.5">
            <div class="w-10 h-10 shrink-0 rounded-full bg-gradient-to-br from-green-300 to-green-500 flex items-center justify-center font-extrabold text-green-950">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0" :class="collapsed && 'lg:hidden'">
                <p class="text-sm font-semibold truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-green-200/60">{{ Auth::user()->isAdminIt() ? 'Admin IT' : 'Admin Roso Sayur' }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}" :class="collapsed && 'lg:hidden'" class="shrink-0">
                @csrf
                <button type="submit" title="Keluar"
                        class="w-9 h-9 rounded-lg flex items-center justify-center text-green-200/70 hover:bg-red-500/20 hover:text-red-200 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>
