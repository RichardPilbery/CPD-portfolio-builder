<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IvsiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ivsites = array(
            array('id' => '1','name' => 'External jugular'),
            array('id' => '2','name' => 'Dorsum'),
            array('id' => '3','name' => 'Antecubital fossa'),
            array('id' => '4','name' => 'Other'),
            array('id' => '5','name' => 'Proximal tibia'),
            array('id' => '6','name' => 'Distal tibia'),
            array('id' => '7','name' => 'Sternum'),
            array('id' => '8','name' => 'Proximal humerus')
          );

          foreach($ivsites as $i) {
              DB::table('ivsites')->insert($i);
          }
    }
}
