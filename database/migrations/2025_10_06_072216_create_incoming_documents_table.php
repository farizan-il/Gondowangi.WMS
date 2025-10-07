<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incoming_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('incoming_id');
            $table->enum('document_type', ['surat_jalan', 'po', 'coa', 'label', 'photo', 'other'])->nullable();
            $table->string('file_name', 255);
            $table->string('file_path', 500);
            $table->integer('file_size')->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('uploaded_by');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('incoming_id')->references('id')->on('incoming_goods');
            $table->foreign('uploaded_by')->references('id')->on('users');
            $table->index('incoming_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incoming_documents');
    }
};