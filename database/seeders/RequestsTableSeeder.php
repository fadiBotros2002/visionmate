<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RequestsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('requests')->insert([
            [
                'blind_id' => 4,
                'volunteer_id' => 2,
                'request_time' => now(),
                'status' => 'accepted',
                'blind_location' => 'Damascus, Abasein',
                'accepted_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'blind_id' => 5,
                'volunteer_id' => 3,
                'request_time' => now(),
                'status' => 'accepted',
                'blind_location' => 'Damascus, almaisat',
                'accepted_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'blind_id' => 5,
                'volunteer_id' => 2,
                'request_time' => now(),
                'status' => 'accepted',
                'blind_location' => 'Damascus, Abasein',
                'accepted_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'blind_id' => 5,
                'volunteer_id' => null,
                'request_time' => now(),
                'status' => 'pending',
                'blind_location' => 'Damascus, Abasein',
                'accepted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'blind_id' => 7,
                'volunteer_id' => null,
                'request_time' => now(),
                'status' => 'pending',
                'blind_location' => 'Damascus, Abasein',
                'accepted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'blind_id' => 8,
                'volunteer_id' => null,
                'request_time' => now(),
                'status' => 'pending',
                'blind_location' => 'Damascus, Abasein',
                'accepted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
