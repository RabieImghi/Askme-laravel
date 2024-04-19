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
            $table->id();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('name');
            $table->string('email', 191)->unique();
            $table->string('password');
            $table->string('points');
            $table->string('country')->nullable();
            $table->string('phone')->nullable();
            $table->string('about')->nullable();
            $table->enum('isBanne', ['0','1'])->default('0');
            $table->string('donnationLink')->nullable();
            $table->string('avatar')->default('default.png');
            $table->string('coverImage')->default('default.jpg');
            $table->unsignedBigInteger('role_id');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->engine = 'InnoDB';
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
