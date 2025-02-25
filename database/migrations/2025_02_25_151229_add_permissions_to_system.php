<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

return new class extends Migration {
    public function up(): void {
        $permissions = [
            'excel.upload',
            // Agrega más permisos aquí si es necesario
        ];

        foreach ($permissions as $permission) {
            if (!Permission::where('name', $permission)->exists()) {
                Permission::create(['name' => $permission, 'guard_name' => 'admin']);
            }
        }
    }

    public function down(): void {
        // Si necesitas eliminar estos permisos al revertir la migración
        Permission::whereIn('name', ['excel.upload'])->delete();
    }
};
