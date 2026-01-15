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