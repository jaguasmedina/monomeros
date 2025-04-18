<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // tus seeders existentes
        $this->call([
            UserSeeder::class,
            AdminSeeder::class,
            RolePermissionSeeder::class,
            // ahora agregamos los nuevos:
            AdminsTableSeeder::class,
            RolesTableSeeder::class,
            RoleHasPermissionsSeeder::class,
        ]);
    }
}
