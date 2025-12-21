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
            $table->string('to_number')->nullable();
            $table->timestamp('to_generated_at')->nullable();
            $table->foreignId('to_generated_by')->nullable()->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservation_requests', function (Blueprint $table) {
            $table->dropForeign(['to_generated_by']);
            $table->dropColumn(['to_number', 'to_generated_at', 'to_generated_by']);
        });
    }
};
