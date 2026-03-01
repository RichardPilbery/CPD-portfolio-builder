<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('activities')->insert([
            'name' => 'Work Based Learning',
            'abbr' => 'WBL',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('activities')->insert([
            'name' => 'Professional activities',
            'abbr' => 'PRO',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('activities')->insert([
            'name' => 'Formal / educational',
            'abbr' => 'EDU',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('activities')->insert([
            'name' => 'Self-directed learning',
            'abbr' => 'SDL',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('activities')->insert([
            'name' => 'Other',
            'abbr' => 'OTH',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
