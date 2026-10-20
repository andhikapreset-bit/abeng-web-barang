<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Dasbor' }} - Logistika</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans text-slate-900 antialiased">
@php
    $menu = [
        'OPERASIONAL' => [
            ['Dasbor', 'dashboard', 'dasbor', null],
            ['Inventaris barang', '#', 'cube', null],
            ['Barang masuk', '#', 'masuk', null],
            ['Barang keluar', '#', 'keluar', null],
            ['Mutasi gudang', '#', 'mutasi', null],
            ['Stok opname', '#', 'opname', null],
        ],
        'PENGELOLAAN' => [
            ['Persetujuan', '#', 'persetujuan', 8],
            ['Laporan', '#', 'laporan', null],
            ['Pengaturan', '#', 'pengaturan', null],
        ],
    ];
    $user = auth()->user();
    $initials = strtoupper(substr($user->name, 0, 2));
@endphp

<aside class="fixed inset-y-0 left-0 z-20 flex w-[232px] flex-col bg-[#14233f] text-slate-300">
    <div class="flex items-center gap-3 px-6 pt-6">
        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-600 text-white">
            <x-icon name="cube" class="h-5 w-5" />
        </div>
        <div>
            <div class="text-lg font-medium leading-none tracking-wide text-white">LOGISTIKA</div>
            <div class="mt-1 text-[10px] tracking-wider text-slate-400">KONSOL INSTITUSI</div>
        </div>
    </div>

    <div class="mx-4 mt-6 flex items-center gap-3 rounded-lg border border-white/10 px-3 py-2.5">
        <x-icon name="gedung" class="h-5 w-5 text-slate-400" />
        <div class="min-w-0 flex-1">
            <div class="truncate text-sm font-semibold text-white">Unit Logistik Pusat</div>
            <div class="text-xs text-slate-400">Jakarta • 3 gudang</div>
        </div>
        <x-icon name="bawah" class="h-4 w-4 text-slate-400" />
    </div>

    <nav class="mt-4 flex-1 overflow-y-auto px-4">
        @foreach ($menu as $group => $items)
            <div class="mb-2 mt-5 px-2 text-[11px] font-semibold tracking-wider text-slate-400">{{ $group }}</div>
            @foreach ($items as [$label, $route, $icon, $badge])
                @php
                    $href = $route === '#' ? '#' : route($route);
                    $active = $route !== '#' && request()->routeIs($route);
                @endphp
                <a href="{{ $href }}" wire:navigate
                   class="relative mb-1 flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition {{ $active ? 'bg-white/10 font-semibold text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    @if ($active)
                        <span class="absolute -left-4 top-1/2 h-6 w-1 -translate-y-1/2 rounded-r bg-blue-500"></span>
                    @endif
                    <x-icon :name="$icon" class="h-[18px] w-[18px]" />
                    <span class="flex-1">{{ $label }}</span>
                    @if ($badge)
                        <span class="rounded-full bg-blue-600/90 px-2 py-0.5 text-xs font-semibold text-white">{{ $badge }}</span>
                    @endif
                </a>
            @endforeach
        @endforeach
    </nav>

    <a href="#" class="mx-4 mb-3 flex items-center gap-3 rounded-lg bg-white/5 px-4 py-3 text-sm text-slate-300 hover:bg-white/10">
        <x-icon name="bantuan" class="h-[18px] w-[18px]" /> Pusat bantuan
    </a>

    <div class="border-t border-white/10 p-4">
        <div class="flex items-center gap-3">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-600 text-xs font-semibold text-white">{{ $initials }}</div>
            <div class="min-w-0 flex-1">
                <div class="truncate text-sm font-semibold text-white">{{ $user->name }}</div>
                <div class="text-xs text-slate-400">Administrator logistik</div>
            </div>
            <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Yakin ingin keluar dari akun?')">
                @csrf
                <button type="submit" title="Keluar" class="rounded-lg p-2 text-slate-400 transition hover:bg-white/10 hover:text-white">
                    <x-icon name="logout" class="h-5 w-5" />
                </button>
            </form>
        </div>
    </div>
</aside>

<div class="ml-[232px] min-h-screen">
    <header class="sticky top-0 z-10 flex h-[76px] items-center justify-between border-b border-slate-200 bg-white px-8">
        <div class="flex items-center gap-2 text-sm text-slate-500">
            <x-icon name="dasbor" class="h-4 w-4" />
            <span>Operasional</span><span>›</span>
            <span class="font-semibold text-slate-900">{{ $title ?? 'Dasbor' }}</span>
        </div>
        <div class="flex items-center gap-5">
            <div class="flex w-80 items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-400">
                <x-icon name="cari" class="h-4 w-4" />
                <span class="flex-1">Cari barang atau dokumen...</span>
                <span class="rounded border border-slate-200 px-1.5 text-[10px]">Ctrl K</span>
            </div>
            <div class="flex items-center gap-2 text-xs text-slate-500">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Sistem terhubung
            </div>
            <div class="relative text-slate-500">
                <x-icon name="lonceng" class="h-5 w-5" />
                <span class="absolute -right-0.5 -top-0.5 h-2 w-2 rounded-full bg-blue-600"></span>
            </div>
            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-50 text-xs font-semibold text-blue-700">{{ $initials }}</div>
        </div>
    </header>

    <main class="p-8">
        {{ $slot }}
    </main>
</div>
</body>
</html>