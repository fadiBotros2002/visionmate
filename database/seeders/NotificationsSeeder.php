<?php

namespace Database\Seeders;

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
                'volunteer_id' => 2,
                'message' => 'There is a request in your area from a blind person. Can you help?',
                'type' => 'request',
                'is_read' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'volunteer_id' => 3,
                'message' => 'Congratulations! You have received a certificate for your volunteering work.',
                'type' => 'certificate',
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'volunteer_id' => 2,
                'message' => 'There is a request in your area from a blind person. Can you help?',
                'type' => 'request',
                'is_read' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
