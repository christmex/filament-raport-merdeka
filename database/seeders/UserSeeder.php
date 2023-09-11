<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $domain = '@sekolahbasic.sch.id';
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'super'.$domain,
                'password' => bcrypt('mantapjiwa00')
            ],
            [
                'name' => 'Jonathan Christian',
                'email' => 'jonathan'.$domain,
                'password' => bcrypt('mantapjiwa00')
            ],
            [
                'name' => 'Dimas',
                'email' => 'dimas'.$domain,
                'password' => bcrypt('mantapjiwa00')
            ],
            [
                'name' => 'Kurni',
                'email' => 'kurni'.$domain,
                'password' => bcrypt('mantapjiwa00')
            ]
        ];
        DB::table('users')->insertOrIgnore($users);
    }
}
