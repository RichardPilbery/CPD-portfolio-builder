<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IvtypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ivtypes = array(
            array('id' => '1','name' => 'IV cannula'),
            array('id' => '2','name' => 'IO: Cooks needle'),
            array('id' => '3','name' => 'IO: Bone injection gun'),
            array('id' => '4','name' => 'IO: EZ-IO'),
            array('id' => '5','name' => 'IO: FAST')
          );

          foreach($ivtypes as $i) {
              DB::table('ivtypes')->insert($i);
          }
    }
}
