<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddObservacionesToMiembrosTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('miembros', function (Blueprint $table) {
            // Agrega el campo observaciones después de 'favorable'
            $table->text('observaciones')->nullable()->after('favorable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('miembros', function (Blueprint $table) {
            // Elimina el campo observaciones
            $table->dropColumn('observaciones');
        });
    }
}
