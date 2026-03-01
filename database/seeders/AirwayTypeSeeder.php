<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AirwayTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $airwaytypes = array(
            array('id' => '1','name' => 'Oropharyngeal airway (OPA)'),
            array('id' => '2','name' => 'Nasopharyngeal airway (NPA)'),
            array('id' => '3','name' => 'Suction - handheld'),
            array('id' => '4','name' => 'Posture (e.g recovery position)'),
            array('id' => '5','name' => 'SAD: Laryngeal mask airway (LMA)'),
            array('id' => '6','name' => 'SAD: i-gel'),
            array('id' => '7','name' => 'Intubation'),
            array('id' => '8','name' => 'Head tilt / chin lift'),
            array('id' => '9','name' => 'Jaw thrust'),
            array('id' => '10','name' => 'SAD: Other'),
            array('id' => '11','name' => 'Needle cricothyroidotomy'),
            array('id' => '12','name' => 'RSA / DAI'),
            array('id' => '13','name' => 'Surgical airway'),
            array('id' => '14','name' => 'BURP'),
            array('id' => '15','name' => 'King-LT'),
            array('id' => '16','name' => 'Suction - manual'),
            array('id' => '17','name' => 'Suction - motorised (e.g. LSU)')
          );

          foreach($airwaytypes as $a) {
              DB::table('airway_activity_types')->insert($a);
          }
    }
}
