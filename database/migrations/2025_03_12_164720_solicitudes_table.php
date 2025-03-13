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
            $table->string('tipo_persona');
            $table->date('fecha_registro');
            $table->string('razon_social');
            $table->string('tipo_id');
            $table->string('identificador', 50);
            $table->text('motivo');
            $table->string('nombre_completo')->nullable();
            $table->string('tipo_cliente')->nullable();
            $table->string('estado')->default('enviado');
            $table->string('tipo_visitante')->nullable();
            $table->string('archivo')->nullable();
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
