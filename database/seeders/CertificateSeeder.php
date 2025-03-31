<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CertificateSeeder extends Seeder
{
    public function run()
    {
        DB::table('certificates')->insert([
            [
                'volunteer_id' => 3,
                'certificate_type' => 'helper',
                'awarded_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}
