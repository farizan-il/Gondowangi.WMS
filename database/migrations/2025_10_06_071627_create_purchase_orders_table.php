<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('no_po', 100)->unique();
            $table->unsignedBigInteger('supplier_id');
            $table->date('tanggal_po');
            $table->date('tanggal_kirim_diharapkan')->nullable();
            $table->decimal('total_nilai', 15, 2)->nullable();
            $table->enum('status', ['pending', 'partial', 'completed', 'cancelled'])->default('pending');
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->foreign('created_by')->references('id')->on('users');
            $table->index('no_po');
            $table->index('supplier_id');
            $table->index('status');
            $table->index('tanggal_po');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};