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
                'blind_latitude' => 33.5146,
                'blind_longitude' => 36.2765,
                'text_request'=> 'I need help walking to the park.',
                'accepted_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'blind_id' => 5,
                'volunteer_id' => 3,
                'request_time' => now(),
                'status' => 'accepted',
                'blind_latitude' => 33.5234,
                'blind_longitude' => 36.2921,
                'text_request'=> 'I need assistance shopping.',
                'accepted_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'blind_id' => 5,
                'volunteer_id' => 2,
                'request_time' => now(),
                'status' => 'accepted',
                'blind_latitude' => 33.5146,
                'blind_longitude' => 36.2765,
                'text_request'=> 'I need assistance shopping.',
                'accepted_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'blind_id' => 5,
                'volunteer_id' => null,
                'request_time' => now(),
                'status' => 'pending',
                'blind_latitude' => 33.5146,
                'blind_longitude' => 36.2765,
                'text_request'=> 'Looking for assistance finding a café.',
                'accepted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'blind_id' => 7,
                'volunteer_id' => null,
                'request_time' => now(),
                'status' => 'pending',
                'blind_latitude' => 33.5146,
                'blind_longitude' => 36.2765,
                'text_request'=> null,
                'accepted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'blind_id' => 8,
                'volunteer_id' => null,
                'request_time' => now(),
                'status' => 'pending',
                'blind_latitude' => 33.5146,
                'blind_longitude' => 36.2765,
                'text_request'=> 'Requesting assistance with directions.',
                'accepted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
