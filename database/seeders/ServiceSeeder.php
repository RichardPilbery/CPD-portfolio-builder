<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $services = array(
            array('id' => '1','title' => 'East Midlands Ambulance Service'),
            array('id' => '2','title' => 'East of England Ambulance Service'),
            array('id' => '4','title' => 'Isle of Wight Ambulance Service'),
            array('id' => '5','title' => 'London Ambulance Service'),
            array('id' => '6','title' => 'North East Ambulance Service'),
            array('id' => '7','title' => 'North West Ambulance Service'),
            array('id' => '8','title' => 'Northern Ireland Ambulance Service'),
            array('id' => '9','title' => 'Scottish Ambulance Service'),
            array('id' => '11','title' => 'South Central Ambulance Service'),
            array('id' => '12','title' => 'South East Coast Ambulance Service'),
            array('id' => '13','title' => 'South Western Ambulance Service'),
            array('id' => '14','title' => 'West Midlands Ambulance Service'),
            array('id' => '15','title' => 'Welsh Ambulance Service'),
            array('id' => '16','title' => 'Yorkshire Ambulance Service'),
            array('id' => '17','title' => 'Other'),
            array('id' => '18','title' => 'Isle of Man Ambulance Service'),
            array('id' => '19','title' => 'Military'),
            array('id' => '20','title' => 'HSE Ambulance Service'),
            array('id' => '21','title' => 'Dublin Fire Brigade'),
            array('id' => '22','title' => 'Order of Malta'),
            array('id' => '23','title' => 'Civil Defence'),
            array('id' => '24','title' => 'St John Ambulance')
        );

          foreach($services as $i) {
              DB::table('services')->insert($i);
          }
    }
}
