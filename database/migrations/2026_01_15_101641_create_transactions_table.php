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
    Schema::create('transactions', function (Blueprint $table) {
        $table->id();
        
        // Relasi ke tabel customers & units
        $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
        $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete();
        
        $table->date('start_date');
        $table->date('end_date');
        $table->decimal('total_price', 12, 2); // Total harga sewa
        
        // Status transaksi
        $table->enum('status', ['booking', 'ongoing', 'completed', 'cancelled'])->default('booking');
        
        $table->text('notes')->nullable(); // Catatan kondisi barang/denda
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
