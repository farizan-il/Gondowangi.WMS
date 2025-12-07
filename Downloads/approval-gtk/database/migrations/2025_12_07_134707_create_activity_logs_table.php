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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('action');
            $table->text('description')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index('user_id');
            // Note: Assuming 'users' table exists, but in this project it seems 'Karyawan' is the user model.
            // However, usually auth uses 'users' or 'karyawan'. 
            // LoginController uses 'Karyawan' model.
            // So I should probably not enforce foreign key constraint strictly if I'm not sure about the table name 
            // or just use user_id as integer. 
            // But looking at LoginController: Auth::guard('web')->login($karyawan...
            // So the user is a Karyawan.
            // I'll check if 'karyawans' table exists to be safe, but for now just storing the ID is enough.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
