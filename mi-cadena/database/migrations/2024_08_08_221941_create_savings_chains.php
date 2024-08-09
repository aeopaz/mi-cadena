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
        Schema::create('savings_chains', function (Blueprint $table) {
            $table->id()->comment('Identificador único de la cadena de ahorro.');
            $table->string('name')->comment('Nombre de la cadena de ahorro');
            $table->unsignedBigInteger('creator_id')->comment('Referencia al usuario que creó la cadena (relación con users');
            $table->integer('participant_count')->comment('Número de participantes en la cadena.');
            $table->double('amount')->comment('Monto de ahorro por participante.');
            $table->tinyInteger('frequency')->comment('Frecuencia de ahorro (7=semanal, 15=quincenal, 30=mensual)..');
            $table->date('start_date')->comment('Fecha de inicio de la cadena.');
            $table->date('end_date')->comment('Fecha de fin de la cadena.');
            $table->enum('status',['A','C','N'])->comment('Estado de la cadena (A=activa, C=completada, N=cancelada).');
            $table->timestamp('deleted_at')->nullable()->comment('Fecha y hora de la eliminacion.');
            $table->timestamps();

            $table->foreign('creator_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('savings_chains');
    }
};
