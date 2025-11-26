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
        Schema::create('cycle_count_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cycle_count_id')->constrained('cycle_counts')->onDelete('cascade');
            $table->foreignId('bin_id')->constrained('warehouse_bins');
            $table->foreignId('material_id')->constrained('materials');
            $table->string('batch_lot')->nullable();
            $table->decimal('system_qty', 15, 2)->default(0);
            $table->decimal('physical_qty', 15, 2)->nullable();
            $table->enum('status', ['pending', 'matched', 'discrepancy'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cycle_count_items');
    }
};
