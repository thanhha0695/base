<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [];
        $roleIdUser = 2;
        for ($i = 0; $i < 100; $i++) {
            $data[] = [
                'username' => "member$i",
                'password' => Hash::make('abc@123'),
                'email' => "member$i@gmail.com",
                'status' => 1,
                'name' => "member$i",
                'gender' => 1,
                'birthday' => '',
                'role_id' => $roleIdUser,
            ];
            if ($i === 49) {
                $roleIdUser = 3;
            }
        }
        $dataUser = [
            [
                'username' => 'admin',
                'password' => Hash::make('abc@2020'),
                'email' => 'thanhha0695@gmail.com',
                'status' => 1,
                'name' => 'Thanh Ha',
                'gender' => 1,
                'birthday' => '',
                'role_id' => 1,
            ],
            [
                'username' => 'manage',
                'password' => Hash::make('manage@2020'),
                'email' => 'manage@gmail.com',
                'status' => 1,
                'name' => 'Manage',
                'gender' => 1,
                'birthday' => '',
                'role_id' => 2,
            ],
        ];
        $dataUserRole = [];
        $toolId = 1;
        $roleId = 2;
        $action = json_encode([
            'view' => true,
            'update' => true,
            'create' => true,
            'delete' => true
        ]);
        for ($i = 0; $i < 10; $i++) {
            $dataUserRole[] = [
                'user_id' => null,
                'role_id' => $roleId,
                'tool_id' => $toolId,
                'action' => $action
            ];
            $toolId++;
            if ($i === 4) {
                $action = json_encode([
                    'view' => true,
                    'update' => false,
                    'create' => false,
                    'delete' => false
                ]);
                $toolId = 1;
                $roleId = 3;
            }

        }
        DB::table('users')->insert(array_merge($dataUser, $data));
        DB::table('permissions')->insert($dataUserRole);
    }
}
