<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'id' => 1,
                'manage_id' => 1,
                'name' => 'Administrator',
                'description' => 'Administrator',
                'parent_id' => 1,
            ],
            [
                'id' => 2,
                'manage_id' => 2,
                'name' => 'manage',
                'description' => 'manage',
                'parent_id' => 2,
            ],
            [
                'id' => 3,
                'manage_id' => 3,
                'name' => 'Member',
                'description' => 'Member',
                'parent_id' => 3,
            ],
        ];
        DB::table('roles')->insert($data);
    }
}
