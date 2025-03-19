<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RatingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('ratings')->insert([
            [
                'blind_id' => 4,
                'volunteer_id' => 2,
                'request_id' => 1,
                'rating' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'blind_id' => 5,
                'volunteer_id' => 3,
                'request_id' => 2,
                'rating' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'blind_id' => 5,
                'volunteer_id' => 2,
                'request_id' => 3,
                'rating' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
