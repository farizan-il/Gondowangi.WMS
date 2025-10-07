<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_zones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warehouse_id');
            $table->string('zone_code', 50);
            $table->string('zone_name', 100);
            $table->enum('zone_type', ['Storage', 'Staging', 'Quarantine', 'Production', 'Shipping', 'Cold Storage', 'Reject'])->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->foreign('warehouse_id')->references('id')->on('warehouses');
            $table->unique(['warehouse_id', 'zone_code'], 'idx_warehouse_zone');
            $table->index('zone_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_zones');
    }
};