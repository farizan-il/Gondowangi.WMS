<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incoming_goods', function (Blueprint $table) {
            $table->id();
            $table->string('incoming_number', 100)->unique();
            $table->string('no_surat_jalan', 100)->nullable();
            $table->unsignedBigInteger('po_id')->nullable();
            $table->unsignedBigInteger('supplier_id');
            $table->string('no_kendaraan', 50)->nullable();
            $table->string('nama_driver', 100)->nullable();
            $table->dateTime('tanggal_terima');
            $table->enum('kategori', ['Raw Material', 'Packaging Material', 'Spare Part', 'Office Supply'])->nullable();
            $table->enum('status', ['Draft', 'Received', 'QC Pending', 'QC Approved', 'QC Rejected', 'Completed'])->default('Draft');
            $table->unsignedBigInteger('received_by');
            $table->timestamps();

            $table->foreign('po_id')->references('id')->on('purchase_orders');
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->foreign('received_by')->references('id')->on('users');
            $table->index('incoming_number');
            $table->index('no_surat_jalan');
            $table->index('tanggal_terima');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incoming_goods');
    }
};