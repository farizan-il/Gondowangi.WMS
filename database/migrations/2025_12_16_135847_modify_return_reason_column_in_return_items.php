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
        DB::statement("ALTER TABLE return_items MODIFY COLUMN return_reason VARCHAR(255) NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We do not revert to ENUM because data might be lost or truncated if we have new values.
        // We keep it as VARCHAR but maybe smaller length if strictly needed, or just do nothing/revert to previous known state if possible.
        // For safety, we can leave it or try to revert.
        // DB::statement("ALTER TABLE return_items MODIFY COLUMN return_reason ENUM('QC Reject', 'Expired', 'Damage', 'Excess Production', 'Wrong Delivery') NULL");
    }
};
