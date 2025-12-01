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
        Schema::create('cycle_counts', function (Blueprint $table) {
            $table->id();
            $table->string('cycle_number'); // Serial number or cycle identifier
            $table->foreignId('material_id')->constrained('materials')->onDelete('cascade');
            $table->foreignId('warehouse_bin_id')->constrained('warehouse_bins')->onDelete('cascade');
            $table->decimal('system_qty', 10, 2); // Quantity in system
            $table->decimal('physical_qty', 10, 2)->nullable(); // Actual counted quantity
            $table->string('scanned_serial')->nullable(); // Scanned serial number
            $table->string('scanned_bin')->nullable(); // Scanned bin location
            $table->timestamp('count_date'); // When the count was performed
            $table->enum('status', ['DRAFT', 'REVIEW_NEEDED', 'APPROVED'])->default('DRAFT');
            $table->text('spv_note')->nullable(); // Supervisor notes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cycle_counts');
    }
};
