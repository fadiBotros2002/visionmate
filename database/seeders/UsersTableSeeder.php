<?php

namespace Database\Seeders;

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
                'username' => 'fadib',
                'phone' => '0936363636',
                'password' => Hash::make('123123123'),
                'role' => 'admin',
                'latitude' => 33.5146,
                'longitude' => 36.2765,
                'identity_image' => 'fadib.png',
                'email' => 'fadibotros99@gmail.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'fadi',
                'phone' => '0938641779',
                'password' => Hash::make('123123123'),
                'role' => 'volunteer',
                'latitude' => 33.5146,
                'longitude' => 36.2765,
                'identity_image' => 'fadi.png',
                'email' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'ahmad',
                'phone' => '0999666332',
                'password' => Hash::make('123123123'),
                'role' => 'volunteer',
                'latitude' => 33.5234,
                'longitude' => 36.2921,
                'identity_image' => 'ahmad.png',
                'email' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'blind',
                'phone' => '0938841777',
                'password' => Hash::make('123123123'),
                'role' => 'blind',
                'latitude' => 33.5146,
                'longitude' => 36.2765,
                'identity_image' => 'blind.png',
                'email' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'blind2',
                'phone' => '0987456321',
                'password' => Hash::make('123123123'),
                'role' => 'blind',
                'latitude' => 33.5112,
                'longitude' => 36.3078,
                'identity_image' => 'blind2.png',
                'email' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'blind3',
                'phone' => '0988885552',
                'password' => Hash::make('123123123'),
                'role' => 'blind',
                'latitude' => 33.5234,
                'longitude' => 36.2921,
                'identity_image' => 'blind3.png',
                'email' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'blind4',
                'phone' => '0932254112',
                'password' => Hash::make('123123123'),
                'role' => 'blind',
                'latitude' => 33.5146,
                'longitude' => 36.2765,
                'identity_image' => 'blind4.png',
                'email' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username' => 'blind5',
                'phone' => '0932254012',
                'password' => Hash::make('123123123'),
                'role' => 'blind',
                'latitude' => 33.5146,
                'longitude' => 36.2765,
                'identity_image' => 'blind5.png',
                'email' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
