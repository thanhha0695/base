<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ToolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tools = [
            [
                'id' => 1,
                'client_id' => 1,
                'name' => 'Dashboard',
                'uri' => '/dashboard',
                'icon' => 'HomeIcon',
                'parent_id' => 1,
                'position' => 1,
            ],
            [
                'id' => 2,
                'client_id' => 1,
                'name' => 'Manage',
                'uri' => '',
                'icon' => 'ToolIcon',
                'parent_id' => 2,
                'position' => 2,
            ],
            [
                'id' => 3,
                'client_id' => 1,
                'name' => 'Manage Users',
                'uri' => '/manage/users',
                'icon' => '',
                'parent_id' => 2,
                'position' => null
            ],
            [
                'id' => 4,
                'client_id' => 1,
                'name' => 'Manage Roles',
                'uri' => '/manage/roles',
                'icon' => '',
                'parent_id' => 2,
                'position' => null
            ],
            [
                'id' => 5,
                'client_id' => 1,
                'name' => 'Manage Tools',
                'uri' => '/manage/tools',
                'icon' => '',
                'parent_id' => 2,
                'position' => null
            ],
        ];
        DB::table('tools')->insert($tools);
    }
}
