<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::auth')] #[Title('Masuk')] class extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Alamat surel wajib diisi.',
            'email.email' => 'Format surel tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            $this->addError('email', 'Surel atau kata sandi salah.');
            return;
        }

        session()->regenerate();
        return $this->redirectRoute('dashboard', navigate: true);
    }
};
?>

<div class="grid min-h-screen lg:grid-cols-2">
    {{-- Panel kiri --}}
    <div class="hidden flex-col bg-[#14233f] px-[72px] py-[72px] text-white lg:flex">
        <div class="flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-600 text-xl">▣</div>
            <span class="text-2xl">LOGISTIKA</span>
        </div>

        <div class="mt-16 text-xs tracking-wide text-slate-300">KONSOL INSTITUSI • UNIT LOGISTIK PUSAT</div>
        <h1 class="mt-5 text-5xl font-bold leading-tight">Persediaan tercatat.<br>Distribusi terkendali.</h1>
        <p class="mt-5 max-w-lg text-slate-300">Satu ruang kerja untuk pencatatan barang masuk, barang keluar, dan pengawasan persediaan instansi.</p>

        <div class="mt-16 rounded-2xl bg-white/5 p-8">
            <div class="flex justify-between text-xs tracking-wide text-slate-300">
                <span>PENERIMAAN</span><span class="text-blue-400">→</span>
                <span>PENYIMPANAN</span><span class="text-blue-400">→</span>
                <span>DISTRIBUSI</span>
            </div>
            <div class="mt-10 flex items-end justify-between text-slate-400/70">
                <div class="h-24 w-44 border border-slate-400/60"></div>
                <div class="h-16 w-20 border border-slate-400/60"></div>
                <div class="h-28 w-32 border border-slate-400/60"></div>
            </div>
            <div class="mt-8 text-xs text-slate-300">Tertib administrasi • Jejak audit jelas • Stok akurat</div>
        </div>

        <div class="mt-auto pt-12 text-xs text-slate-400">© 2026 Unit Logistik Pusat. Akses khusus pegawai instansi.</div>
    </div>

    {{-- Panel kanan --}}
    <div class="flex items-center justify-center px-8 py-12">
        <div class="w-full max-w-[560px]" x-data="{ show: false }">
            <div class="text-xs font-semibold tracking-wide text-blue-600">SELAMAT DATANG KEMBALI</div>
            <h2 class="mt-3 text-3xl font-bold text-slate-900">Masuk ke akun Anda</h2>
            <p class="mt-3 text-sm text-slate-500">Gunakan akun instansi untuk mengakses konsol logistik.</p>

            <form wire:submit="login" class="mt-8 space-y-6">
                <div>
                    <label class="mb-2 block text-xs font-semibold text-slate-900">Alamat surel instansi</label>
                    <input type="email" wire:model="email" placeholder="nama@logistik.go.id"
                           class="w-full rounded-lg border border-slate-200 px-3 py-3 text-sm outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
                    @error('email') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-2 block text-xs font-semibold text-slate-900">Kata sandi</label>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'" wire:model="password"
                               class="w-full rounded-lg border border-slate-200 px-3 py-3 pr-16 text-sm outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
                        <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-500 hover:text-slate-800" x-text="show ? 'Sembunyikan' : 'Tampilkan'"></button>
                    </div>
                    @error('password') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2 text-slate-500">
                        <input type="checkbox" wire:model="remember" class="rounded border-slate-300"> Ingat perangkat ini
                    </label>
                    <a href="#" class="text-blue-600 hover:underline">Lupa kata sandi?</a>
                </div>

                <button type="submit" wire:loading.attr="disabled"
                        class="w-full rounded-lg bg-blue-600 py-3.5 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-60">
                    <span wire:loading.remove>Masuk ke konsol</span>
                    <span wire:loading>Memproses...</span>
                </button>
            </form>

            <div class="mt-6 rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-xs text-slate-500">
                Akses Anda dicatat untuk keamanan dan audit. Jangan bagikan akun atau kata sandi kepada pihak lain.
            </div>
            <hr class="my-7 border-slate-200">
            <p class="text-xs text-slate-500">Belum memiliki akses? Hubungi administrator instansi melalui bantuan@logistik.go.id.</p>
        </div>
    </div>
</div>