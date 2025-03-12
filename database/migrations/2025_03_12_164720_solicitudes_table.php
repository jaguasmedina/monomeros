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
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_persona'); // natural o jurídica
            $table->date('fecha_registro');
            $table->string('razon_social');
            $table->string('tipo_id');
            $table->string('identificador', 50);
            $table->text('motivo');
            $table->string('nombre_completo')->nullable(); // No requerido
            $table->string('tipo_visitante')->nullable(); // Solo para persona natural
            $table->string('archivo')->nullable(); // PDF, no requerido
            $table->string('tipo_cliente')->nullable(); // Solo para persona jurídica
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitudes');
    }
};
