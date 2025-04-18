<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UnifiedModificationsToSolicitudesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Agregar los nuevos campos a la tabla 'solicitudes'
        Schema::table('solicitudes', function (Blueprint $table) {
            // Agregar campo 'concepto' si no existe
            if (! Schema::hasColumn('solicitudes', 'concepto')) {
                $table->string('concepto', 45)->nullable()->after('updated_at');
            }
            // Campo 'concepto_sagrilaft'
            if (! Schema::hasColumn('solicitudes', 'concepto_sagrilaft')) {
                $table->string('concepto_sagrilaft', 45)->nullable()->after('concepto');
            }
            // Campo 'concepto_ptee'
            if (! Schema::hasColumn('solicitudes', 'concepto_ptee')) {
                $table->string('concepto_ptee', 45)->nullable()->after('concepto_sagrilaft');
            }
            // Campo 'motivo_rechazo'
            if (! Schema::hasColumn('solicitudes', 'motivo_rechazo')) {
                $table->string('motivo_rechazo', 500)->nullable()->after('concepto_ptee');
            }
        });

        // 2. Modificar la columna 'archivo' a tipo TEXT y nullable.
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->text('archivo')->nullable()->change();
        });

        // 3. Unificar la modificación de 'admin_id'
        // Primero, eliminar la columna (si ya existe) y sus claves foráneas.
        Schema::table('solicitudes', function (Blueprint $table) {
            if (Schema::hasColumn('solicitudes', 'admin_id')) {
                // Se asume que existe una clave foránea, la eliminamos.
                $table->dropForeign(['admin_id']);
                $table->dropColumn('admin_id');
            }
        });
        // Luego, agregar la columna 'admin_id' con la clave foránea apuntando a 'admins'.
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->foreignId('admin_id')
                  ->nullable()
                  ->after('updated_at')
                  ->constrained('admins')
                  ->onUpdate('cascade')
                  ->onDelete('set null')
                  ->comment('Usuario administrador asignado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Se revertirán los cambios realizados en 'solicitudes'
        Schema::table('solicitudes', function (Blueprint $table) {
            // Primero, eliminar la clave foránea y la columna admin_id
            if (Schema::hasColumn('solicitudes', 'admin_id')) {
                $table->dropForeign(['admin_id']);
                $table->dropColumn('admin_id');
            }
            // Eliminar los campos agregados
            if (Schema::hasColumn('solicitudes', 'motivo_rechazo')) {
                $table->dropColumn('motivo_rechazo');
            }
            if (Schema::hasColumn('solicitudes', 'concepto_ptee')) {
                $table->dropColumn('concepto_ptee');
            }
            if (Schema::hasColumn('solicitudes', 'concepto_sagrilaft')) {
                $table->dropColumn('concepto_sagrilaft');
            }
            if (Schema::hasColumn('solicitudes', 'concepto')) {
                $table->dropColumn('concepto');
            }
            // Revertir la columna 'archivo' a string(255)
            $table->string('archivo', 255)->nullable()->change();
        });
    }
}
