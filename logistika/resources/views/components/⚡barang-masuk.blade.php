<?php

use App\Models\Barang;
use App\Models\Gudang;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Barang masuk')] class extends Component
{
    public string $barang_id = '';
    public string $gudang_id = '';
    public string $jumlah = '';
    public string $pihak = '';
    public string $catatan = '';

    #[Computed]
    public function daftarBarang()
    {
        return Barang::orderBy('nama')->get();
    }

    #[Computed]
    public function daftarGudang()
    {
        return Gudang::orderBy('nama')->get();
    }

    #[Computed]
    public function riwayat()
    {
        return Transaksi::with(['barang', 'gudang'])
            ->where('jenis', 'masuk')
            ->latest()->latest('id')
            ->limit(8)->get();
    }

    public function simpan()
    {
        $this->validate([
            'barang_id' => 'required|exists:barang,id',
            'gudang_id' => 'required|exists:gudang,id',
            'jumlah' => 'required|integer|min:1|max:1000000',
            'pihak' => 'required|string|max:100',
            'catatan' => 'nullable|string|max:255',
        ], [
            'barang_id.required' => 'Pilih barang terlebih dahulu.',
            'gudang_id.required' => 'Pilih gudang tujuan.',
            'jumlah.required' => 'Jumlah wajib diisi.',
            'jumlah.integer' => 'Jumlah harus berupa angka bulat.',
            'jumlah.min' => 'Jumlah minimal 1.',
            'pihak.required' => 'Asal pemasok wajib diisi.',
        ]);

        $trx = DB::transaction(fn () => Transaksi::create([
            'nomor' => Transaksi::buatNomor('masuk'),
            'jenis' => 'masuk',
            'barang_id' => (int) $this->barang_id,
            'gudang_id' => (int) $this->gudang_id,
            'jumlah' => (int) $this->jumlah,
            'pihak' => $this->pihak,
            'status' => 'Selesai',
            'catatan' => $this->catatan ?: null,
            'user_id' => auth()->id(),
        ]));

        $this->reset(['barang_id', 'gudang_id', 'jumlah', 'pihak', 'catatan']);
        unset($this->riwayat);

        session()->flash('sukses', "Barang masuk {$trx->nomor} berhasil dicatat.");
    }
};
?>

@php
    $dipilih = $this->daftarBarang->firstWhere('id', (int) $barang_id);
    $input = 'w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100';
@endphp

<div>
    <div class="mb-6">
        <h1 class="text-3xl font-bold">Catat barang masuk</h1>
        <p class="mt-2 text-sm text-slate-500">Catat penerimaan barang dari pemasok. Stok bertambah otomatis setelah disimpan.</p>
    </div>

    @if (session('sukses'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('sukses') }}</div>
    @endif

    <div class="grid gap-6 xl:grid-cols-[480px_1fr]">
        <form wire:submit="simpan" class="space-y-5 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div>
                <label class="mb-2 block text-xs font-semibold">Barang</label>
                <select wire:model.live="barang_id" class="{{ $input }}">
                    <option value="">Pilih barang...</option>
                    @foreach ($this->daftarBarang as $b)
                        <option value="{{ $b->id }}">{{ $b->kode }} - {{ $b->nama }}</option>
                    @endforeach
                </select>
                @error('barang_id') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-2 block text-xs font-semibold">Gudang tujuan</label>
                <select wire:model="gudang_id" class="{{ $input }}">
                    <option value="">Pilih gudang...</option>
                    @foreach ($this->daftarGudang as $g)
                        <option value="{{ $g->id }}">{{ $g->nama }}</option>
                    @endforeach
                </select>
                @error('gudang_id') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-2 block text-xs font-semibold">Jumlah diterima</label>
                <div class="flex items-center gap-3">
                    <input type="number" min="1" wire:model="jumlah" placeholder="0" class="{{ $input }}">
                    <span class="w-20 text-sm text-slate-500">{{ $dipilih?->satuan ?? 'satuan' }}</span>
                </div>
                @error('jumlah') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-2 block text-xs font-semibold">Asal (pemasok)</label>
                <input type="text" wire:model="pihak" placeholder="Contoh: PT Sumber Kertas" class="{{ $input }}">
                @error('pihak') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-2 block text-xs font-semibold">Catatan (opsional)</label>
                <textarea wire:model="catatan" rows="3" class="{{ $input }}"></textarea>
                @error('catatan') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <button type="submit" wire:loading.attr="disabled"
                    class="w-full rounded-lg bg-blue-600 py-3 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-60">
                <span wire:loading.remove wire:target="simpan">Simpan barang masuk</span>
                <span wire:loading wire:target="simpan">Menyimpan...</span>
            </button>
        </form>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-4">
                <h2 class="text-lg font-semibold">Penerimaan terbaru</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs text-slate-500">
                        <tr>
                            <th class="px-6 py-3 font-medium">No. transaksi</th>
                            <th class="px-3 py-3 font-medium">Barang</th>
                            <th class="px-3 py-3 font-medium">Jumlah</th>
                            <th class="px-3 py-3 font-medium">Gudang</th>
                            <th class="px-3 py-3 font-medium">Asal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($this->riwayat as $t)
                            <tr>
                                <td class="px-6 py-3">
                                    <div class="font-semibold text-blue-600">{{ $t->nomor }}</div>
                                    <div class="text-xs text-slate-400">{{ $t->created_at->format('d/m/Y H.i') }}</div>
                                </td>
                                <td class="px-3 py-3 font-medium">{{ $t->barang->nama }}</td>
                                <td class="px-3 py-3 font-semibold">+{{ number_format($t->jumlah, 0, ',', '.') }} {{ $t->barang->satuan }}</td>
                                <td class="px-3 py-3 text-slate-500">{{ $t->gudang->nama }}</td>
                                <td class="px-3 py-3">{{ $t->pihak }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-10 text-center text-slate-400">Belum ada barang masuk.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>