<?php

use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Dasbor')] class extends Component
{
    public string $tab = 'semua';
    public string $search = '';
};
?>

@php
    $sumSql = "COALESCE(SUM(CASE WHEN jenis = 'masuk' THEN jumlah ELSE -jumlah END), 0)";
    $hariIni = fn ($jenis) => \App\Models\Transaksi::where('jenis', $jenis)->whereDate('created_at', today());

    $totalStok = (int) \App\Models\Transaksi::selectRaw("$sumSql as t")->value('t');
    $masukUnit = (int) $hariIni('masuk')->sum('jumlah');
    $masukTrx = $hariIni('masuk')->count();
    $keluarUnit = (int) $hariIni('keluar')->sum('jumlah');
    $keluarTrx = $hariIni('keluar')->count();

    // Stok per barang untuk peringatan stok minimum
    $semuaBarang = \App\Models\Barang::query()->select('barang.*')
        ->selectSub(
            \App\Models\Transaksi::query()->selectRaw($sumSql)->whereColumn('transaksi.barang_id', 'barang.id'),
            'stok_total'
        )->get();
    $rendah = $semuaBarang->filter(fn ($b) => $b->stok_total < $b->stok_minimum)->sortBy('stok_total')->values();

    // Grafik 7 hari
    $mulai = today()->subDays(6);
    $rows = \App\Models\Transaksi::where('created_at', '>=', $mulai)
        ->selectRaw('DATE(created_at) as tgl, jenis, SUM(jumlah) as total')
        ->groupBy('tgl', 'jenis')->get();

    $days = $masuk = $keluar = [];
    for ($i = 6; $i >= 0; $i--) {
        $d = today()->subDays($i);
        $key = $d->toDateString();
        $days[] = $d->locale('id')->translatedFormat('D j M');
        $masuk[] = (int) $rows->where('tgl', $key)->where('jenis', 'masuk')->sum('total');
        $keluar[] = (int) $rows->where('tgl', $key)->where('jenis', 'keluar')->sum('total');
    }
    $max = max(1, max($masuk), max($keluar));
    $step = $max <= 50 ? 10 : ($max <= 500 ? 100 : 500);
    $top = (int) (ceil($max / $step) * $step);
    $labelsY = [$top, (int) round($top * 2 / 3), (int) round($top / 3), 0];

    // Tabel transaksi
    $trx = \App\Models\Transaksi::with(['barang', 'gudang'])
        ->when($tab !== 'semua', fn ($q) => $q->where('jenis', $tab))
        ->when($search !== '', fn ($q) => $q->where(fn ($w) => $w
            ->where('nomor', 'like', "%{$search}%")
            ->orWhereHas('barang', fn ($b) => $b->where('nama', 'like', "%{$search}%"))))
        ->latest()->latest('id')->limit(10)->get();
    $totalTrx = \App\Models\Transaksi::count();
    $trxHariIni = \App\Models\Transaksi::whereDate('created_at', today())->count();

    $badge = [
        'Selesai' => 'bg-emerald-50 text-emerald-700',
        'Menunggu' => 'bg-amber-50 text-amber-700',
        'Diperiksa' => 'bg-blue-50 text-blue-700',
    ];
    $fmt = fn ($n) => number_format($n, 0, ',', '.');
@endphp

<div>
    {{-- Judul --}}
    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-3xl font-bold">Dasbor operasional</h1>
            <p class="mt-2 text-sm text-slate-500">Pantau persediaan dan arus barang seluruh gudang dalam satu tempat.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('barang.keluar') }}" wire:navigate class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold hover:bg-slate-50"><x-icon name="keluar" class="h-4 w-4" /> Catat barang keluar</a>
            <a href="{{ route('barang.masuk') }}" wire:navigate class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700"><x-icon name="tambah" class="h-4 w-4" /> Catat barang masuk</a>
        </div>
    </div>

    <div class="mt-6 flex items-center justify-between text-sm">
        <span class="text-xs text-slate-400">Terakhir diperbarui pukul {{ now()->format('H.i') }} WIB</span>
        <div class="rounded-lg border border-slate-200 bg-white px-4 py-2.5">Hari ini, {{ now()->locale('id')->translatedFormat('j F Y') }}</div>
    </div>

    {{-- Kartu statistik --}}
    <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between text-sm text-slate-500"><span>Total stok tersedia</span><x-icon name="cube" class="h-5 w-5 text-blue-600" /></div>
            <div class="mt-3 flex items-baseline gap-2"><span class="text-4xl font-bold">{{ $fmt($totalStok) }}</span><span class="text-sm text-slate-500">unit</span></div>
            <div class="mt-3 text-xs text-slate-500">di seluruh gudang</div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between text-sm text-slate-500"><span>Barang masuk</span><x-icon name="masuk" class="h-5 w-5 text-blue-600" /></div>
            <div class="mt-3 flex items-baseline gap-2"><span class="text-4xl font-bold">{{ $fmt($masukUnit) }}</span><span class="text-sm text-slate-500">unit</span></div>
            <div class="mt-3 text-xs text-slate-500"><span class="font-semibold text-slate-900">{{ $masukTrx }} transaksi</span> penerimaan hari ini</div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between text-sm text-slate-500"><span>Barang keluar</span><x-icon name="keluar" class="h-5 w-5 text-blue-600" /></div>
            <div class="mt-3 flex items-baseline gap-2"><span class="text-4xl font-bold">{{ $fmt($keluarUnit) }}</span><span class="text-sm text-slate-500">unit</span></div>
            <div class="mt-3 text-xs text-slate-500"><span class="font-semibold text-slate-900">{{ $keluarTrx }} transaksi</span> distribusi hari ini</div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between text-sm text-slate-500"><span>Stok di bawah minimum</span><x-icon name="peringatan" class="h-5 w-5 text-amber-500" /></div>
            <div class="mt-3 flex items-baseline gap-2"><span class="text-4xl font-bold">{{ $rendah->count() }}</span><span class="text-sm text-slate-500">jenis barang</span></div>
            <div class="mt-3 text-xs text-slate-500"><span class="font-semibold text-amber-600">{{ $rendah->count() > 0 ? 'Perlu pengadaan' : 'Aman' }}</span> dari {{ $semuaBarang->count() }} jenis barang</div>
        </div>
    </div>

    {{-- Grafik + perlu perhatian --}}
    <div class="mt-6 grid gap-4 xl:grid-cols-[1fr_360px]">
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div>
                <h2 class="text-lg font-semibold">Aktivitas barang</h2>
                <p class="text-sm text-slate-500">{{ $mulai->locale('id')->translatedFormat('j F') }} – {{ today()->locale('id')->translatedFormat('j F Y') }}</p>
            </div>

            <div class="mt-3 flex gap-6 text-xs text-slate-500">
                <span><span class="mr-1.5 inline-block h-2 w-2 bg-blue-600"></span>Barang masuk <b class="ml-1 text-slate-900">{{ $fmt(array_sum($masuk)) }} unit</b></span>
                <span><span class="mr-1.5 inline-block h-2 w-2 bg-slate-400"></span>Barang keluar <b class="ml-1 text-slate-900">{{ $fmt(array_sum($keluar)) }} unit</b></span>
            </div>

            <div class="mt-6 flex">
                <div class="flex h-[160px] flex-col justify-between pr-3 text-right text-[11px] text-slate-400">
                    @foreach ($labelsY as $l) <span>{{ $fmt($l) }}</span> @endforeach
                </div>
                <div class="flex-1">
                    <div class="flex h-[160px] items-end justify-around border-b border-slate-200">
                        @foreach ($days as $i => $d)
                            <div class="flex h-full items-end gap-1">
                                <div class="w-5 rounded-t bg-blue-600" style="height: {{ $masuk[$i] / $top * 100 }}%" title="Masuk {{ $fmt($masuk[$i]) }}"></div>
                                <div class="w-5 rounded-t bg-slate-400" style="height: {{ $keluar[$i] / $top * 100 }}%" title="Keluar {{ $fmt($keluar[$i]) }}"></div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-2 flex justify-around text-[11px] text-slate-400">
                        @foreach ($days as $d) <span>{{ $d }}</span> @endforeach
                    </div>
                </div>
            </div>

            @php $selisih = array_sum($masuk) - array_sum($keluar); @endphp
            <div class="mt-6 flex justify-between text-sm">
                <span class="text-slate-500">Selisih bersih</span>
                <span class="font-semibold {{ $selisih >= 0 ? 'text-emerald-700' : 'text-red-600' }}">{{ $selisih >= 0 ? '+' : '−' }}{{ $fmt(abs($selisih)) }} unit dalam 7 hari</span>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <h2 class="inline-flex items-center gap-2 text-lg font-semibold text-amber-700"><x-icon name="peringatan" class="h-5 w-5" /> Perlu perhatian</h2>
                <span class="rounded-md bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">{{ $rendah->count() }} barang</span>
            </div>
            <p class="mt-4 text-sm text-slate-500">Stok berikut berada di bawah batas minimum.</p>

            <div class="mt-4 divide-y divide-slate-100">
                @forelse ($rendah->take(4) as $b)
                    <div class="flex items-center justify-between py-3">
                        <div>
                            <div class="text-sm font-medium">{{ $b->nama }}</div>
                            <div class="text-xs text-slate-400">{{ $b->kode }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-semibold text-amber-700">{{ $fmt($b->stok_total) }} {{ $b->satuan }}</div>
                            <div class="text-xs text-slate-400">Min. {{ $fmt($b->stok_minimum) }} {{ $b->satuan }}</div>
                        </div>
                    </div>
                @empty
                    <div class="py-6 text-center text-sm text-slate-400">Semua stok di atas batas minimum.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Tabel transaksi --}}
    <div class="mt-6 rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="p-6">
            <div class="flex items-center gap-3">
                <h2 class="text-lg font-semibold">Transaksi barang</h2>
                <span class="rounded-md bg-slate-100 px-2.5 py-1 text-xs text-slate-500">{{ $trxHariIni }} hari ini</span>
            </div>

            <div class="mt-5 flex items-center justify-between">
                <div class="flex gap-2 text-sm">
                    @foreach (['semua' => 'Semua transaksi', 'masuk' => 'Barang masuk', 'keluar' => 'Barang keluar'] as $key => $label)
                        <button wire:click="$set('tab', '{{ $key }}')"
                                class="rounded-lg px-4 py-2 {{ $tab === $key ? 'bg-blue-50 font-semibold text-blue-700' : 'text-slate-500 hover:bg-slate-50' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
                <div class="flex w-72 items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-sm">
                    <x-icon name="cari" class="h-4 w-4 text-slate-400" />
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari barang, nomor transaksi..." class="w-full bg-transparent outline-none placeholder:text-slate-400">
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-y border-slate-200 bg-slate-50 text-xs text-slate-500">
                    <tr>
                        <th class="px-6 py-3 font-medium">No. transaksi / waktu</th>
                        <th class="px-3 py-3 font-medium">Nama barang</th>
                        <th class="px-3 py-3 font-medium">Jenis</th>
                        <th class="px-3 py-3 font-medium">Jumlah</th>
                        <th class="px-3 py-3 font-medium">Gudang</th>
                        <th class="px-3 py-3 font-medium">Asal / tujuan</th>
                        <th class="px-3 py-3 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($trx as $t)
                        @php $masukJenis = $t->jenis === 'masuk'; @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-blue-600">{{ $t->nomor }}</div>
                                <div class="text-xs text-slate-400">{{ $t->created_at->locale('id')->translatedFormat('j M Y') }} • {{ $t->created_at->format('H.i') }}</div>
                            </td>
                            <td class="px-3 py-4">
                                <div class="font-semibold">{{ $t->barang->nama }}</div>
                                <div class="text-xs text-slate-400">{{ $t->barang->kode }}</div>
                            </td>
                            <td class="px-3 py-4 {{ $masukJenis ? 'text-blue-600' : 'text-slate-500' }}">{{ $masukJenis ? 'Masuk' : 'Keluar' }}</td>
                            <td class="px-3 py-4 font-semibold">{{ $masukJenis ? '+' : '−' }}{{ $fmt($t->jumlah) }} {{ $t->barang->satuan }}</td>
                            <td class="px-3 py-4 text-slate-500">{{ $t->gudang->nama }}</td>
                            <td class="px-3 py-4">
                                <div>{{ $t->pihak }}</div>
                                <div class="text-xs text-slate-400">{{ $masukJenis ? 'Pemasok' : 'Unit penerima' }}</div>
                            </td>
                            <td class="px-3 py-4">
                                <span class="rounded-md px-2.5 py-1 text-xs font-semibold {{ $badge[$t->status] ?? 'bg-slate-100 text-slate-600' }}">{{ $t->status }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-10 text-center text-slate-400">Belum ada transaksi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-6 py-4 text-xs text-slate-500">
            Menampilkan {{ $trx->count() }} transaksi terbaru dari total {{ $fmt($totalTrx) }} transaksi
        </div>
    </div>
</div>