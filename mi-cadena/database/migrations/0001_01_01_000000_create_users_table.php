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
            $table->id()->comment('Identificador único del usuario.');
            $table->string('name')->comment('Nombre completo del usuario.');
            $table->string('email')->unique()->comment('Correo electrónico del usuario (único).');
            $table->string('mobile')->unique()->comment('Número celular  del usuario (único).');
            $table->string('code_email_verify')->nullable()->comment('Código para verificar email.');
            $table->timestamp('email_verified_at')->nullable()->comment('Fecha y hora de la verificación de correo electrónico.');
            $table->string('password')->comment('Contraseña cifrada.');
            $table->rememberToken();
            $table->timestamp('deleted_at')->nullable()->comment('Fecha y hora de la eliminacion.');
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
