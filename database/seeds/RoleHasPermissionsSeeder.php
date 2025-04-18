<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleHasPermissionsSeeder extends Seeder
{
    public function run()
    {
        $pivot = [
            ['permission_id' =>  2, 'role_id' => 1],
            ['permission_id' =>  3, 'role_id' => 1],
            ['permission_id' =>  4, 'role_id' => 1],
            ['permission_id' =>  5, 'role_id' => 1],
            ['permission_id' =>  6, 'role_id' => 1],
            ['permission_id' =>  7, 'role_id' => 1],
            ['permission_id' =>  8, 'role_id' => 1],
            ['permission_id' =>  9, 'role_id' => 1],
            ['permission_id' => 10, 'role_id' => 1],
            ['permission_id' => 11, 'role_id' => 1],
            ['permission_id' => 12, 'role_id' => 1],
            ['permission_id' => 13, 'role_id' => 1],
            ['permission_id' => 14, 'role_id' => 1],
            ['permission_id' => 15, 'role_id' => 1],
            ['permission_id' => 16, 'role_id' => 1],
            ['permission_id' => 17, 'role_id' => 1],
            ['permission_id' => 18, 'role_id' => 1],
            ['permission_id' => 19, 'role_id' => 1],
            ['permission_id' => 20, 'role_id' => 1],
            ['permission_id' => 21, 'role_id' => 1],

            ['permission_id' =>  2, 'role_id' => 2],
            ['permission_id' =>  7, 'role_id' => 2],
            ['permission_id' => 16, 'role_id' => 2],
            ['permission_id' => 18, 'role_id' => 2],
            ['permission_id' => 19, 'role_id' => 2],
            ['permission_id' => 20, 'role_id' => 2],

            ['permission_id' =>  2, 'role_id' => 3],
            ['permission_id' =>  7, 'role_id' => 3],
            ['permission_id' => 16, 'role_id' => 3],
            ['permission_id' => 18, 'role_id' => 3],
            ['permission_id' => 19, 'role_id' => 3],
            ['permission_id' => 20, 'role_id' => 3],

            ['permission_id' =>  2, 'role_id' => 4],
            ['permission_id' =>  7, 'role_id' => 4],
            ['permission_id' => 16, 'role_id' => 4],
            ['permission_id' => 18, 'role_id' => 4],
            ['permission_id' => 19, 'role_id' => 4],
            ['permission_id' => 20, 'role_id' => 4],

            ['permission_id' =>  2, 'role_id' => 5],
            ['permission_id' =>  7, 'role_id' => 5],
            ['permission_id' => 16, 'role_id' => 5],
            ['permission_id' => 18, 'role_id' => 5],
            ['permission_id' => 19, 'role_id' => 5],
            ['permission_id' => 20, 'role_id' => 5],

            ['permission_id' =>  2, 'role_id' => 6],
            ['permission_id' =>  7, 'role_id' => 6],
            ['permission_id' =>  8, 'role_id' => 6],
            ['permission_id' => 16, 'role_id' => 6],
            ['permission_id' => 18, 'role_id' => 6],
            ['permission_id' => 19, 'role_id' => 6],
            ['permission_id' => 20, 'role_id' => 6],
        ];

        foreach ($pivot as $row) {
            DB::table('role_has_permissions')
              ->updateOrInsert(
                  ['permission_id' => $row['permission_id'], 'role_id' => $row['role_id']],
                  $row
              );
        }
    }
}
