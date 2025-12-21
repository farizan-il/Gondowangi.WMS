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
        Schema::create('material_removal_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_request_id')->constrained('reservation_requests')->onDelete('cascade');
            $table->foreignId('reservation_id')->nullable(); // ID dari allocation yang dihapus
            $table->string('material_code');
            $table->string('material_name');
            $table->string('batch_lot');
            $table->date('expiry_date')->nullable();
            $table->decimal('qty_removed', 10, 2);
            $table->string('uom', 10);
            $table->enum('removal_reason', ['expired', 'near-expiry', 'manual'])->default('manual');
            $table->integer('days_until_expiry')->nullable(); // Negative if expired
            $table->foreignId('removed_by')->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_removal_logs');
    }
};
