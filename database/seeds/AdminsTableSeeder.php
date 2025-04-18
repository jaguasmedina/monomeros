<?php

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminsTableSeeder extends Seeder
{
    public function run()
    {
        $admins = [
            [
                'id'             => 1,
                'name'           => 'Super Admin',
                'email'          => 'superadmin@monomeros.com',
                'username'       => 'superadmin',
                'password'       => '$2y$12$us/F8Xr9CW8Vwb0XVjGZZO8Ly9KRuEo1ysmAFhRGHXzY5k7KDpGym',
                'created_at'     => '2025-04-08 21:59:58',
                'updated_at'     => '2025-04-08 21:59:58',
            ],
            [
                'id'             => 2,
                'name'           => 'visualizador',
                'email'          => 'visualizador@monomeros.com',
                'username'       => 'visualizador',
                'password'       => '$2y$12$9XAirR362FyA66w5w0/3tuEJapdZxl11vQ3Fb9m0bu5vmdLtXJGQq',
                'created_at'     => '2025-04-09 12:03:09',
                'updated_at'     => '2025-04-09 12:03:09',
            ],
            [
                'id'             => 3,
                'name'           => 'analista',
                'email'          => 'analista@monomeros.com',
                'username'       => 'analista',
                'password'       => '$2y$12$9fMOF.BOcbT6QH54q0RZo.hVC.LO7DgxnY8kkxCnezsS2vT2rRvxG',
                'created_at'     => '2025-04-09 12:03:31',
                'updated_at'     => '2025-04-09 12:03:31',
            ],
            [
                'id'             => 4,
                'name'           => 'sagrilaft',
                'email'          => 'sagrilaft@monomeros.com',
                'username'       => 'sagrilaft',
                'password'       => '$2y$12$JXdBc3rZmCFKbgQDFcF93ucBL6LlCO5lZnAFHsYEVP0v.0KfcyAxK',
                'created_at'     => '2025-04-09 12:03:53',
                'updated_at'     => '2025-04-09 12:03:53',
            ],
            [
                'id'             => 5,
                'name'           => 'ptee',
                'email'          => 'ptee@monomeros.com',
                'username'       => 'ptee',
                'password'       => '$2y$12$AWx2naSlsFdV9lI5TBgAUO0ThjJnBCSGO6K0AshOSulG9b8q.c6gS',
                'created_at'     => '2025-04-09 12:04:16',
                'updated_at'     => '2025-04-09 12:04:16',
            ],
            [
                'id'             => 6,
                'name'           => 'usuario',
                'email'          => 'usuario@monomeros1.com',
                'username'       => 'usuario',
                'password'       => '$2y$12$RuYS6bYJErU8ol9nyLC18elocmK0OX48JwzTbGBoaaeSqiMz8J2EG',
                'created_at'     => '2025-04-09 12:04:50',
                'updated_at'     => '2025-04-09 12:05:37',
            ],
        ];

        foreach ($admins as $data) {
            Admin::updateOrCreate(['id' => $data['id']], $data);
        }
    }
}
