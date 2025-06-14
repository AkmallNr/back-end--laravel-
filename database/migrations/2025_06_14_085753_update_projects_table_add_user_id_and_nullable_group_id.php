<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Ubah groupId menjadi nullable
            $table->foreignId('groupId')->nullable()->change();
            // Tambahkan kolom userId
            $table->foreignId('userId')->constrained('users')->onDelete('cascade')->after('groupId');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Kembalikan groupId ke constrained (tidak nullable)
            $table->foreignId('groupId')->constrained('groups')->onDelete('cascade')->change();
            // Hapus kolom userId
            $table->dropColumn('userId');
        });
    }
};
