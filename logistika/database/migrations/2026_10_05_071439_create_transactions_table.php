<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
    Schema::create('transactions', function (Blueprint $table) {
        $table->id();
        $table->string('transaction_no')->unique();
        $table->enum('type', ['in', 'out']);
        $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
        $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
        $table->string('origin_destination');
        $table->integer('qty');
        $table->enum('status', ['Selesai', 'Menunggu', 'Diperiksa']);
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
