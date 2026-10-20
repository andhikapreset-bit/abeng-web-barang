<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Gudang;
use App\Models\Transaksi;
use Illuminate\Database\Seeder;

class LogistikSeeder extends Seeder
{
    public function run(): void
    {
        $gudang = collect(['Gudang Utama', 'Gudang Selatan', 'Gudang Timur'])
            ->map(fn ($nama) => Gudang::firstOrCreate(['nama' => $nama]));

        $items = [
            ['ATK-001', 'Kertas A4 80 gsm', 'rim', 100],
            ['ATK-018', 'Toner printer hitam', 'unit', 25],
            ['ATK-023', 'Map arsip A4', 'buah', 200],
            ['APD-002', 'Masker medis 3 lapis', 'kotak', 40],
            ['APD-007', 'Sarung tangan nitril', 'kotak', 50],
            ['KSM-012', 'Air mineral 600 ml', 'karton', 60],
        ];

        foreach ($items as [$kode, $nama, $satuan, $min]) {
            $barang = Barang::firstOrCreate(
                ['kode' => $kode],
                ['nama' => $nama, 'satuan' => $satuan, 'stok_minimum' => $min]
            );

            if (! Transaksi::where('barang_id', $barang->id)->exists()) {
                Transaksi::create([
                    'nomor' => 'BM-AWAL-' . $kode,
                    'jenis' => 'masuk',
                    'barang_id' => $barang->id,
                    'gudang_id' => $gudang[0]->id,
                    'jumlah' => $min * 2,
                    'pihak' => 'Stok awal',
                    'status' => 'Selesai',
                ]);
            }
        }
    }
}