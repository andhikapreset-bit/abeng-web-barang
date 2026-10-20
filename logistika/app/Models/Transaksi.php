<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';
    protected $guarded = [];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function gudang()
    {
        return $this->belongsTo(Gudang::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Stok = total masuk - total keluar (opsional per gudang). */
    public static function stok(int $barangId, ?int $gudangId = null): int
    {
        $query = static::where('barang_id', $barangId);

        if ($gudangId) {
            $query->where('gudang_id', $gudangId);
        }

        return (int) $query
            ->selectRaw("COALESCE(SUM(CASE WHEN jenis = 'masuk' THEN jumlah ELSE -jumlah END), 0) as total")
            ->value('total');
    }

    /** Contoh: BM-261020-001 (masuk) atau BK-261020-001 (keluar). */
    public static function buatNomor(string $jenis): string
    {
        $prefix = ($jenis === 'masuk' ? 'BM' : 'BK') . '-' . now()->format('ymd') . '-';
        $urut = static::where('nomor', 'like', $prefix . '%')->count() + 1;

        return $prefix . str_pad((string) $urut, 3, '0', STR_PAD_LEFT);
    }
}