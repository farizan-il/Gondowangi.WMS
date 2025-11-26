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
            $table->string('reference_number')->unique();
            $table->foreignId('assigned_user_id')->nullable()->constrained('users');
            $table->foreignId('warehouse_id')->constrained('warehouses'); // Assuming warehouses table exists, or just integer
            $table->enum('status', ['draft', 'in_progress', 'completed', 'cancelled'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
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
