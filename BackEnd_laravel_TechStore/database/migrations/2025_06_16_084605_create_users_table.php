<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Họ tên
            $table->string('email')->unique();
            $table->string('password'); // Mật khẩu (đã được hash)

            $table->integer('email_otp')->nullable();
            $table->boolean('email_verified')->default(false);
            $table->string('role')->default('user'); // "user" hoặc "admin"

            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('avatar')->nullable();

            $table->rememberToken();
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
