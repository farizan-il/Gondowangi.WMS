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
        Schema::create('material_reqc', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained('materials')->onDelete('cascade');
            $table->foreignId('inventory_stock_id')->constrained('inventory_stock')->onDelete('cascade');
            $table->string('batch_lot');
            $table->date('old_exp_date');
            $table->date('new_exp_date')->nullable();
            $table->foreignId('bin_from_id')->constrained('warehouse_bins')->onDelete('cascade');
            $table->foreignId('bin_qrt_id')->nullable()->constrained('warehouse_bins')->onDelete('set null');
            $table->decimal('qty', 10, 2);
            $table->enum('status', ['PENDING_TRANSFER', 'IN_QRT', 'APPROVED', 'REJECTED'])->default('PENDING_TRANSFER');
            $table->text('qc_notes')->nullable();
            $table->foreignId('qc_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('qc_date')->nullable();
            $table->timestamps();

            $table->index(['material_id', 'status']);
            $table->index('inventory_stock_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_reqc');
    }
};
