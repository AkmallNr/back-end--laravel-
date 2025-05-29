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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('deadline')->nullable();
            $table->string('reminder')->nullable();
            $table->string('priority')->nullable();
            $table->boolean('status')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('quoteId')->nullable();
            $table->foreignId('projectId')->constrained('projects')->onDelete('cascade');
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
