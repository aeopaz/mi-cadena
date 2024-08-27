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
        Schema::create('contributions', function (Blueprint $table) {
            $table->id(); // id (PK): Identificador único de la aportación
            $table->unsignedBigInteger('participant_id'); // FK a Participants
            $table->decimal('amount', 10, 2); // Monto de la aportación
            $table->date('estimated_contribution_date'); // Fecha en que se realizó la aportación
            $table->date('real_contribution_date')->nullable(); // Fecha en que se realizó la aportación
            $table->string('status')->comment('Estado de la aportación (D: Debe, P:Pagado)'); // 
            $table->timestamp('deleted_at')->nullable()->comment('Fecha y hora de la eliminacion.');
            $table->timestamps();

            $table->foreign('participant_id')->references('id')->on('participants'); // FK a Participants
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contributions');
    }
};
