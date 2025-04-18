<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovimientosSolicitudesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('movimientos_solicitudes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('solicitud_id');
            $table->string('estado_anterior', 50)->nullable();
            $table->string('estado_nuevo', 50);
            $table->text('comentario')->nullable();
            $table->timestamp('fecha_movimiento')->useCurrent(); // o puedes usar dateTime
            $table->timestamps();

            // Clave foránea, ajusta 'solicitudes' si tu tabla se llama distinto
            $table->foreign('solicitud_id')
                  ->references('id')->on('solicitudes')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_solicitudes');
    }
}
