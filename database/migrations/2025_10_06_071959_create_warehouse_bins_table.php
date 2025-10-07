<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_bins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('zone_id');
            $table->unsignedBigInteger('warehouse_id');
            $table->string('bin_code', 50)->unique();
            $table->string('bin_name', 100);
            $table->enum('bin_type', ['Normal', 'Quarantine', 'Reject', 'Staging', 'Production'])->default('Normal')->comment('Jenis bin untuk filtering');
            $table->integer('capacity')->nullable()->comment('Maximum items/pallets');
            $table->integer('current_items')->default(0);
            $table->enum('status', ['available', 'full', 'maintenance', 'blocked', 'inactive'])->default('available');
            $table->timestamps();

            $table->foreign('zone_id')->references('id')->on('warehouse_zones');
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
            $table->index('bin_code');
            $table->index('zone_id');
            $table->index('bin_type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_bins');
    }
};