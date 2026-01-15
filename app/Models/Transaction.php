<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    // Agar kolom bisa diisi
    protected $guarded = [];

    // Mengubah format tanggal agar mudah diolah
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // --- TAMBAHKAN KODE INI (MAGIC LOGIC) ---
    protected static function booted()
    {
        // 1. Saat Transaksi BARU dibuat (Created)
        static::created(function ($transaction) {
            // Ubah status unit menjadi 'rented'
            $transaction->unit()->update(['status' => 'rented']);
        });

        // 2. Saat Transaksi DIUPDATE (Misal status diganti jadi Selesai/Batal)
        static::updated(function ($transaction) {
            // Jika status transaksi berubah jadi 'completed' (Barang kembali)
            if ($transaction->status === 'completed') {
                $transaction->unit()->update(['status' => 'ready']);
            }
            
            // Jika status transaksi dibatalkan
            if ($transaction->status === 'cancelled') {
                $transaction->unit()->update(['status' => 'ready']);
            }
        });
    }

    // Relasi: Transaksi milik Customer
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    // Relasi: Transaksi milik Unit
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}