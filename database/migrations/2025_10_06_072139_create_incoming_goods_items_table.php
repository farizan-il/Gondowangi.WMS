<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incoming_goods_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('incoming_id');
            $table->unsignedBigInteger('material_id');
            $table->string('batch_lot', 100)->nullable();
            $table->date('exp_date')->nullable();
            $table->decimal('qty_wadah', 10, 2)->nullable();
            $table->decimal('qty_unit', 10, 2)->nullable();
            $table->string('satuan', 50)->nullable();
            $table->boolean('kondisi_baik')->default(false);
            $table->boolean('kondisi_tidak_baik')->default(false);
            $table->boolean('coa_ada')->default(false);
            $table->boolean('coa_tidak_ada')->default(false);
            $table->boolean('label_mfg_ada')->default(false);
            $table->boolean('label_mfg_tidak_ada')->default(false);
            $table->boolean('label_coa_sesuai')->default(false);
            $table->boolean('label_coa_tidak_sesuai')->default(false);
            $table->string('pabrik_pembuat', 200)->nullable();
            $table->enum('status_qc', ['To QC', 'In QC', 'PASS', 'REJECT'])->default('To QC');
            $table->text('qr_code')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('incoming_id')->references('id')->on('incoming_goods');
            $table->foreign('material_id')->references('id')->on('materials');
            $table->index('incoming_id');
            $table->index('batch_lot');
            $table->index('status_qc');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incoming_goods_items');
    }
};