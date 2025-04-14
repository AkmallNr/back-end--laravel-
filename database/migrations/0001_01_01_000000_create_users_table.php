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
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // otomatis bigint dan auto-increment
            $table->string('name');
            $table->string('email')->unique()->nullable(); // nullable sesuai model Java
            $table->string('pass')->nullable();
            $table->timestamps();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();         // ID unik untuk session
            $table->foreignId('userId')->nullable(); // ID user (jika user login)
            $table->string('ip_address', 45)->nullable(); // Alamat IP pengguna
            $table->text('user_agent')->nullable();  // User agent (browser info)
            $table->text('payload');                 // Isi session (dalam bentuk serialized)
            $table->integer('last_activity');        // Timestamp aktivitas terakhir
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('sessions');

    }
};
