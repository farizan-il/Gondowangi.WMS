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
        Schema::create('qc_reqc_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('qc_checklist_id')->nullable()->comment('FK to qc_checklists after Re-QC completed');
            $table->unsignedBigInteger('inventory_stock_id')->comment('FK to inventory_stock');
            $table->unsignedBigInteger('incoming_item_id')->nullable()->comment('FK to incoming_goods_items');
            $table->string('reqc_number')->unique()->comment('REQC/YYYYMMDD/0001');
            $table->enum('old_status', ['PASS', 'REJECT', 'To QC'])->comment('Status before Re-QC');
            $table->date('old_exp_date')->nullable()->comment('Exp date before Re-QC');
            $table->text('reason')->default('Material Expired')->comment('Reason for Re-QC');
            $table->unsignedBigInteger('initiated_by')->comment('User who initiated Re-QC');
            $table->timestamp('initiated_at')->comment('When Re-QC was initiated');
            $table->enum('status', ['PENDING', 'COMPLETED', 'CANCELLED'])->default('PENDING')->comment('Re-QC status');
            $table->enum('new_status', ['PASS', 'REJECT'])->nullable()->comment('Status after Re-QC completed');
            $table->date('new_exp_date')->nullable()->comment('New exp date if PASS');
            $table->decimal('qty_sample_previous', 10, 2)->nullable()->comment('Previous cumulative sample qty');
            $table->decimal('qty_sample_new', 10, 2)->nullable()->comment('New sample qty taken');
            $table->timestamp('completed_at')->nullable()->comment('When Re-QC was completed');
            $table->timestamps();
            
            // Skip foreign keys to avoid constraint errors
            // Foreign keys can be added manually later if needed
            
            $table->index(['inventory_stock_id', 'status']);
            $table->index('reqc_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qc_reqc_history');
    }
};
