<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('notifications')->insert([
            [
                'volunteer_id' => 2, // volunteer_id
                'message' => 'There is a request in your area from a blind person. Can you help?',
                'is_read' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'volunteer_id' => 3, // volunteer_id
                'message' => 'There is a request in your area from a blind person. Can you help?',
                'is_read' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'volunteer_id' => 2, // volunteer_id
                'message' => 'There is a request in your area from a blind person. Can you help?',
                'is_read' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
