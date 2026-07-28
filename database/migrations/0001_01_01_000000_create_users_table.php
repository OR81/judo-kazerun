<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The users table carries no email address and no password.
     *
     * Identity here is the mobile number and nothing else: members sign in with a
     * one-time code sent by SMS, administrators included. There is therefore no
     * password to reset, so Laravel's password_reset_tokens table is gone too.
     * The mobile column itself is added by the extend migration, alongside the
     * other member fields.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // Kept for «مرا به خاطر بسپار»: the session guard's remember cookie is
            // independent of how the session was first established.
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('sessions');
    }
};
