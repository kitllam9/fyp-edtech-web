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

        /**
         * Laravel default `users` table is reserved for the web end.
         * The project has renamed it to `admins`, and used `users` for the API.
         * This is to drop the duplicated table before creating the actual one. 
         */
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->unsignedInteger('points')->default(0);
            $table->longText('badges')->nullable();
            $table->longText('interests')->nullable();
            $table->unsignedBigInteger('group_id')->nullable();
            $table->longText('finished_quests')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
