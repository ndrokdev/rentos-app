<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('units', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // Nama Mobil/Motor/Kamera
        $table->string('plate_number')->unique()->nullable(); // Plat nomor (unik). Nullable jika rental kamera
        $table->decimal('price_per_day', 12, 2); // Harga sewa per hari
        $table->enum('status', ['ready', 'rented', 'maintenance'])->default('ready');
        $table->string('image')->nullable(); // Foto unit
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
