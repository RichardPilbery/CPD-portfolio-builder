<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CapnographySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $capnographies = array(
            array('id' => '1','name' => 'Waveform capnography'),
            array('id' => '2','name' => 'Capnometry'),
            array('id' => '3','name' => 'Colorimetric'),
            array('id' => '4','name' => 'Other'),
            array('id' => '5','name' => 'Positube')
          );

          foreach($capnographies as $c) {
              DB::table('capnographies')->insert($c);
          }
    }
}
