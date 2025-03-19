<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                //1
                'username' => 'fadib',
                'phone' => '0936363636',
                'password' => Hash::make('123123123'),
                'role' => 'admin',
                'location' => 'Damascus, Abasein',
                'identity_image' => 'fadib.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                //2
                'username' => 'fadi',
                'phone' => '0938641779',
                'password' => Hash::make('123123123'),
                'role' => 'volunteer',
                'location' => 'Damascus, Abasein',
                'identity_image' => 'fadi.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                //3
                'username' => 'ahmad',
                'phone' => '0999666332',
                'password' => Hash::make('123123123'),
                'role' => 'volunteer',
                'location' => 'Damascus, almaisat',
                'identity_image' => 'ahmad.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                //4
                'username' => 'blind',
                'phone' => '0938841777',
                'password' => Hash::make('123123123'),
                'role' => 'blind',
                'location' => 'Damascus, Abasein',
                'identity_image' => 'blind.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                //5
                'username' => 'blind2',
                'phone' => '0987456321',
                'password' => Hash::make('123123123'),
                'role' => 'blind',
                'location' => 'Damascus, babtuma',
                'identity_image' => 'blind2.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                //6
                'username' => 'blind3',
                'phone' => '0988885552',
                'password' => Hash::make('123123123'),
                'role' => 'blind',
                'location' => 'Damascus, almaisat',
                'identity_image' => 'blind3.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                //7
                'username' => 'blind4',
                'phone' => '0932254112',
                'password' => Hash::make('123123123'),
                'role' => 'blind',
                'location' => 'Damascus, Abasein',
                'identity_image' => 'blind4.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                //8
                'username' => 'blind5',
                'phone' => '0932254012',
                'password' => Hash::make('123123123'),
                'role' => 'blind',
                'location' => 'Damascus, Abasein',
                'identity_image' => 'blind5.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}
