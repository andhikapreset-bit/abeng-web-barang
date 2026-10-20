<?php

use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Dasbor')] class extends Component
{
    public string $tab = 'semua';
};
?>

@php
    $stats = [
        ['Total stok tersedia', '24.580', 'unit', '+4,2%', 'dibanding bulan lalu', 'text-emerald-600'],
        ['Barang masuk', '1.248', 'unit', '32 transaksi', 'penerimaan hari ini', 'text-slate-900'],
        ['Barang keluar', '856', 'unit', '24 transaksi', 'distribusi hari ini', 'text-slate-900'],
        ['Stok di bawah minimum', '12', 'jenis barang', 'Perlu pengadaan', 'dari 348 jenis barang', 'text-amber-600'],
    ];

    $days = ['Sen 28 Sep', 'Sel 29 Sep', 'Rab 30 Sep', 'Kam 1 Okt', 'Jum 2 Okt', 'Sab 3 Okt', 'Min 4 Okt'];
    $masuk = [760, 980, 830, 1150, 1020, 860, 1240];
    $keluar = [520, 640, 590, 790, 730, 830, 820];

    $alerts = [
        ['Kertas A4 80 gsm', 'ATK-001 • Gudang Utama', '24 rim', 'Min. 100 rim'],
        ['Toner printer hitam', 'ATK-018 • Gudang Utama', '8 unit', 'Min. 25 unit'],
        ['Sarung tangan nitril', 'APD-007 • Gudang Utama', '15 kotak', 'Min. 50 kotak'],
    ];

    $trx = [
        ['BM-261004-032', '4 Okt 2026 • 14.25', 'Kertas A4 80 gsm', 'ATK-001', 'Masuk', '+200 rim', 'Gudang Utama', 'PT Sumber Kertas', 'Pemasok', 'Selesai'],
        ['BK-261004-024', '4 Okt 2026 • 14.10', 'Toner printer hitam', 'ATK-018', 'Keluar', '−12 unit', 'Gudang Utama', 'Bagian Administrasi', 'Unit penerima', 'Menunggu'],
        ['BM-261004-031', '4 Okt 2026 • 13.48', 'Sarung tangan nitril', 'APD-007', 'Masuk', '+150 kotak', 'Gudang Selatan', 'PT Medika Nusantara', 'Pemasok', 'Diperiksa'],
        ['BK-261004-023', '4 Okt 2026 • 13.30', 'Air mineral 600 ml', 'KSM-012', 'Keluar', '−80 karton', 'Gudang Utama', 'Bagian Umum', 'Unit penerima', 'Selesai'],
        ['BM-261004-030', '4 Okt 2026 • 12.55', 'Map arsip A4', 'ATK-023', 'Masuk', '+300 buah', 'Gudang Timur', 'CV Mitra Perkantoran', 'Pemasok', 'Selesai'],
        ['BK-261004-022', '4 Okt 2026 • 11.42', 'Masker medis 3 lapis', 'APD-002', 'Keluar', '−50 kotak', 'Gudang Selatan', 'Unit Pelayanan', 'Unit penerima', 'Selesai'],
    ];

    $badge = [
        'Selesai' => 'bg-emerald-50 text-emerald-700',
        'Menunggu' => 'bg-amber-50 text-amber-700',
        'Diperiksa' => 'bg-blue-50 text-blue-700',
    ];

    $filtered = collect($trx)->filter(fn ($r) => match ($tab) {
        'masuk' => $r[4] === 'Masuk',
        'keluar' => $r[4] === 'Keluar',
        default => true,
    });
@endphp

<div>
    {{-- Judul --}}
    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-3xl font-bold">Dasbor operasional</h1>
            <p class="mt-2 text-sm text-slate-500">Pantau persediaan dan arus barang seluruh gudang dalam satu tempat.</p>
        </div>
        <div class="flex gap-3">
            <a href="#" class="rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold hover:bg-slate-50">↑ Catat barang keluar</a>
            <a href="#" class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">+ Catat barang masuk</a>
        </div>
    </div>

    {{-- Filter --}}
    <div class="mt-6 flex items-center justify-between">
        <div class="flex items-center gap-4 text-sm">
            <div class="rounded-lg border border-slate-200 bg-white px-4 py-2.5 font-medium">Semua gudang ⌄</div>
            <span class="text-xs text-slate-400">Terakhir diperbarui pukul {{ now()->format('H.i') }} WIB</span>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm">Hari ini, {{ now()->translatedFormat('j F Y') }} ⌄</div>
    </div>

    {{-- Kartu statistik --}}
    <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach ($stats as [$label, $value, $unit, $note, $noteLabel, $noteColor])
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <div class="text-sm text-slate-500">{{ $label }}</div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-4xl font-bold">{{ $value }}</span>
                    <span class="text-sm text-slate-500">{{ $unit }}</span>
                </div>
                <div class="mt-3 text-xs text-slate-500"><span class="font-semibold {{ $noteColor }}">{{ $note }}</span> {{ $noteLabel }}</div>
            </div>
        @endforeach
    </div>

    {{-- Grafik + perlu perhatian --}}
    <div class="mt-6 grid gap-4 xl:grid-cols-[1fr_360px]">
        <div class="rounded-xl border border-slate-200 bg-white p-6">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-lg font-semibold">Aktivitas barang</h2>
                    <p class="text-sm text-slate-500">28 September – 4 Oktober 2026</p>
                </div>
                <div class="flex rounded-lg border border-slate-200 text-xs">
                    <span class="rounded-lg bg-blue-50 px-3 py-1.5 font-semibold text-blue-700">7 hari</span>
                    <span class="px-3 py-1.5 text-slate-500">30 hari</span>
                    <span class="px-3 py-1.5 text-slate-500">90 hari</span>
                </div>
            </div>

            <div class="mt-3 flex gap-6 text-xs text-slate-500">
                <span><span class="mr-1.5 inline-block h-2 w-2 bg-blue-600"></span>Barang masuk <b class="ml-1 text-slate-900">{{ number_format(array_sum($masuk), 0, ',', '.') }} unit</b></span>
                <span><span class="mr-1.5 inline-block h-2 w-2 bg-slate-400"></span>Barang keluar <b class="ml-1 text-slate-900">{{ number_format(array_sum($keluar), 0, ',', '.') }} unit</b></span>
            </div>

            <div class="mt-6 flex">
                <div class="flex h-[160px] flex-col justify-between pr-3 text-right text-[11px] text-slate-400">
                    <span>1.500</span><span>1.000</span><span>500</span><span>0</span>
                </div>
                <div class="flex-1">
                    <div class="flex h-[160px] items-end justify-around border-b border-slate-200">
                        @foreach ($days as $i => $d)
                            <div class="flex h-full items-end gap-1">
                                <div class="w-5 rounded-t bg-blue-600" style="height: {{ $masuk[$i] / 1500 * 100 }}%"></div>
                                <div class="w-5 rounded-t bg-slate-400" style="height: {{ $keluar[$i] / 1500 * 100 }}%"></div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-2 flex justify-around text-[11px] text-slate-400">
                        @foreach ($days as $d) <span>{{ $d }}</span> @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-between text-sm">
                <span class="text-slate-500">Selisih bersih</span>
                <span class="font-semibold text-emerald-700">+{{ number_format(array_sum($masuk) - array_sum($keluar), 0, ',', '.') }} unit dalam 7 hari</span>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-amber-700">⚠ Perlu perhatian</h2>
                <span class="rounded-md bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">12 barang</span>
            </div>
            <p class="mt-4 text-sm text-slate-500">Stok berikut berada di bawah batas minimum.</p>

            <div class="mt-4 divide-y divide-slate-100">
                @foreach ($alerts as [$name, $meta, $qty, $min])
                    <div class="flex items-center justify-between py-3">
                        <div>
                            <div class="text-sm font-medium">{{ $name }}</div>
                            <div class="text-xs text-slate-400">{{ $meta }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-semibold text-amber-700">{{ $qty }}</div>
                            <div class="text-xs text-slate-400">{{ $min }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <a href="#" class="mt-4 flex items-center justify-between text-sm font-semibold text-blue-600">
                <span>Lihat semua stok minimum</span><span>→</span>
            </a>
        </div>
    </div>

    {{-- Tabel transaksi --}}
    <div class="mt-6 rounded-xl border border-slate-200 bg-white">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <h2 class="text-lg font-semibold">Transaksi barang</h2>
                    <span class="rounded-md bg-slate-100 px-2.5 py-1 text-xs text-slate-500">56 hari ini</span>
                </div>
                <button class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-semibold hover:bg-slate-50">↓ Ekspor</button>
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
                <div class="flex gap-3 text-sm">
                    <div class="w-64 rounded-lg border border-slate-200 px-3 py-2 text-slate-400">Cari barang, nomor transaksi...</div>
                    <div class="rounded-lg border border-slate-200 px-4 py-2 font-medium">Semua status ⌄</div>
                    <div class="rounded-lg border border-slate-200 px-4 py-2 font-semibold">Filter</div>
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
                        <th class="px-3 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($filtered as [$no, $time, $name, $sku, $type, $qty, $wh, $party, $partyRole, $status])
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-blue-600">{{ $no }}</div>
                                <div class="text-xs text-slate-400">{{ $time }}</div>
                            </td>
                            <td class="px-3 py-4">
                                <div class="font-semibold">{{ $name }}</div>
                                <div class="text-xs text-slate-400">{{ $sku }}</div>
                            </td>
                            <td class="px-3 py-4 {{ $type === 'Masuk' ? 'text-blue-600' : 'text-slate-500' }}">{{ $type === 'Masuk' ? '↙' : '↗' }} {{ $type }}</td>
                            <td class="px-3 py-4 font-semibold">{{ $qty }}</td>
                            <td class="px-3 py-4 text-slate-500">{{ $wh }}</td>
                            <td class="px-3 py-4">
                                <div>{{ $party }}</div>
                                <div class="text-xs text-slate-400">{{ $partyRole }}</div>
                            </td>
                            <td class="px-3 py-4">
                                <span class="rounded-md px-2.5 py-1 text-xs font-semibold {{ $badge[$status] }}">{{ $status }}</span>
                            </td>
                            <td class="px-3 py-4 text-slate-400">•••</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex items-center justify-between border-t border-slate-200 px-6 py-4 text-xs text-slate-500">
            <span>Menampilkan 1–{{ $filtered->count() }} dari 56 transaksi</span>
            <div class="flex items-center gap-2">
                <span class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-900">‹ Sebelumnya</span>
                <span class="rounded-lg bg-blue-50 px-3 py-2 text-blue-700">1</span>
                <span class="px-2">2</span><span class="px-2">3</span><span class="px-2">…</span><span class="px-2">10</span>
                <span class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-900">Berikutnya ›</span>
            </div>
        </div>
    </div>
</div>