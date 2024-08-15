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
        Schema::create('participants', function (Blueprint $table) {
            $table->id(); // id (PK): Identificador único del participante
            $table->unsignedBigInteger('saving_chain_id'); // FK a SavingChain
            $table->unsignedBigInteger('user_id'); // FK a Users
            $table->timestamp('joined_at')->nullable(); // Fecha y hora en que el usuario se unió a la cadena
            $table->string('role'); // Rol del participante (creador, participante)
            $table->integer('turn_order'); // Orden en que el participante recibirá su aporte
            $table->enum('status',['A','R','P'])->comment('Estado de la participación: A: Aceptar, R: Rechazar, P:Pendiente.'); // Orden en que el participante recibirá su aporte
            $table->timestamp('deleted_at')->nullable()->comment('Fecha y hora de la eliminacion.');
            $table->timestamps(); // created_at y updated_at
            $table->foreign('saving_chain_id')->references('id')->on('savings_chains'); // FK a SavingChain
            $table->foreign('user_id')->references('id')->on('users'); // FK a Users
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
