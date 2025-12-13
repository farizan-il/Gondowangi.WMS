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
        Schema::table('reservation_requests', function (Blueprint $table) {
            $table->timestamp('picking_started_at')->nullable()->after('status');
            $table->timestamp('picking_completed_at')->nullable()->after('picking_started_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservation_requests', function (Blueprint $table) {
            $table->dropColumn(['picking_started_at', 'picking_completed_at']);
        });
    }
};
