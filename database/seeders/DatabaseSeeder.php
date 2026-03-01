<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ActivitySeeder::class);
        $this->call(AirwayTypeSeeder::class);
        $this->call(AuditItemSeeder::class);
        $this->call(CapnographySeeder::class);
        $this->call(ClfSeeder::class);
        $this->call(IvsiteSeeder::class);
        $this->call(IvtypeSeeder::class);
        $this->call(KsfSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(ServiceSeeder::class);
        $this->call(UserSeeder::class);

    }
}
