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
        Schema::create('content', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->string('description', 255);
            $table->enum('type', ['notes', 'exercise'])->default('notes');
            $table->string('pdf_url', 100)->nullable();
            $table->longText('exercise_details')->nullable();
            $table->longText('tags')->nullable();
            $table->enum('difficulty', ['easy', 'medium', 'advanced'])->default('easy');
            $table->integer('points')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content');
    }
};
