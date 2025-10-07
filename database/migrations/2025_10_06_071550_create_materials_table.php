<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('kode_item', 50)->unique();
            $table->string('nama_material', 200);
            $table->enum('kategori', ['Raw Material', 'Packaging Material', 'Spare Part', 'Office Supply', 'Finished Goods'])->nullable();
            $table->string('satuan', 50)->nullable()->comment('UoM');
            $table->text('deskripsi')->nullable();
            $table->boolean('qc_required')->default(true)->comment('Apakah material ini perlu QC');
            $table->boolean('expiry_required')->default(false)->comment('Apakah material ini punya exp date');
            $table->enum('abc_class', ['A', 'B', 'C'])->nullable()->comment('ABC classification for inventory management');
            $table->unsignedBigInteger('default_supplier_id')->nullable()->comment('Default supplier untuk material ini');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->foreign('default_supplier_id')->references('id')->on('suppliers');
            $table->index('kode_item');
            $table->index('kategori');
            $table->index('abc_class');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};