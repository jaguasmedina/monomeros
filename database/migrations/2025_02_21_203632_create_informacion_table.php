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
        Schema::create('informacion', function (Blueprint $table) {
            $table->string('identificador')->primary();
            $table->string('tipo');
            $table->string('nombre_completo');
            $table->string('empresa');
            $table->date('fecha_registro');
            $table->date('fecha_vigencia');
            $table->string('cargo');
            $table->string('estado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('informacion');
    }
};
