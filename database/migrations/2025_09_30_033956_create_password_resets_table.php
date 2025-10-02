<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('password_resets', function (Blueprint $table) {
            $table->id();
            $table->string('email', 100);
            $table->string('token');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('expired_at')->nullable();
            
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_resets');
    }
};