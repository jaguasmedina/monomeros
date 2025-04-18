<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['id' => 1, 'name' => 'superadmin', 'guard_name' => 'admin', 'created_at' => '2025-04-08 21:59:58', 'updated_at' => '2025-04-08 21:59:58'],
            ['id' => 2, 'name' => 'visualizador', 'guard_name' => 'admin', 'created_at' => '2025-04-08 22:14:49', 'updated_at' => '2025-04-08 22:14:49'],
            ['id' => 3, 'name' => 'analista',    'guard_name' => 'admin', 'created_at' => '2025-04-09 12:01:40', 'updated_at' => '2025-04-09 12:01:40'],
            ['id' => 4, 'name' => 'sagrilaft',   'guard_name' => 'admin', 'created_at' => '2025-04-09 12:01:59', 'updated_at' => '2025-04-09 12:01:59'],
            ['id' => 5, 'name' => 'ptee',        'guard_name' => 'admin', 'created_at' => '2025-04-09 12:02:17', 'updated_at' => '2025-04-09 12:02:17'],
            ['id' => 6, 'name' => 'usuarios',    'guard_name' => 'admin', 'created_at' => '2025-04-09 12:02:38', 'updated_at' => '2025-04-09 12:05:04'],
        ];

        foreach ($roles as $data) {
            Role::updateOrCreate(['id' => $data['id']], $data);
        }
    }
}
