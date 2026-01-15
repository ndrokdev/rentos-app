<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    // Agar semua kolom bisa diisi
    protected $guarded = [];

    // Casting agar tipe data sesuai (opsional tapi bagus)
    protected $casts = [
        'price_per_day' => 'decimal:2',
    ];
}
