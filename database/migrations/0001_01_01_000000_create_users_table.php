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
        // Membuat tabel users
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Kolom nama pengguna
            $table->string('email')->unique(); // Kolom email dengan unique constraint
            $table->timestamp('email_verified_at')->nullable(); // Kolom untuk verifikasi email
            $table->string('password'); // Kolom password
            $table->rememberToken(); // Kolom untuk "remember me" token
            $table->timestamps(); // Kolom created_at dan updated_at
        });

        // Membuat tabel password_reset_tokens
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary(); // Kolom email sebagai primary key
            $table->string('token'); // Kolom token reset password
            $table->timestamp('created_at')->nullable(); // Kolom untuk mencatat waktu pembuatan token
        });

        // Membuat tabel sessions untuk menyimpan sesi pengguna
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary(); // Kolom ID sesi sebagai primary key
            $table->foreignId('user_id')->nullable()->index(); // Kolom user_id yang mengacu pada tabel users
            $table->string('ip_address', 45)->nullable(); // Kolom untuk menyimpan IP address pengguna
            $table->text('user_agent')->nullable(); // Kolom untuk menyimpan user agent (browser info)
            $table->longText('payload'); // Kolom untuk menyimpan data sesi
            $table->integer('last_activity')->index(); // Kolom untuk menyimpan waktu terakhir aktivitas
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Menghapus tabel users
        Schema::dropIfExists('users');

        // Menghapus tabel password_reset_tokens
        Schema::dropIfExists('password_reset_tokens');

        // Menghapus tabel sessions
        Schema::dropIfExists('sessions');
    }
};
