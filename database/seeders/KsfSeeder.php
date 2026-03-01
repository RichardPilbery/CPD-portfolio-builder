<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KsfSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ksfs = array(
            array('id' => '1','name' => 'Core 1','description' => 'Communication'),
            array('id' => '2','name' => 'Core 2','description' => 'Personal and People Development'),
            array('id' => '3','name' => 'Core 3','description' => 'Health, Safety and Security'),
            array('id' => '4','name' => 'Core 4','description' => 'Service Improvement'),
            array('id' => '5','name' => 'Core 5','description' => 'Quality'),
            array('id' => '6','name' => 'Core 6','description' => 'Equality and Diversity'),
            array('id' => '7','name' => 'HWB1','description' => 'Promotion of health and wellbeing and prevention of adverse effects to health and wellbeing'),
            array('id' => '8','name' => 'HWB2','description' => 'Assessment and care planning to meet people’s health and wellbeing needs'),
            array('id' => '9','name' => 'HWB3','description' => 'Protection of health and wellbeing'),
            array('id' => '10','name' => 'HWB4','description' => 'Enablement to address health and wellbeing needs'),
            array('id' => '11','name' => 'HWB5','description' => 'Provision of care to meet health and wellbeing needs'),
            array('id' => '12','name' => 'HWB6','description' => 'Assessment and treatment planning'),
            array('id' => '13','name' => 'HWB7','description' => 'Interventions and treatments'),
            array('id' => '14','name' => 'HWB8','description' => 'Biomedical investigatoin and intervention'),
            array('id' => '15','name' => 'HWB9','description' => 'Equipment and devices to meet health and wellbeing needs'),
            array('id' => '16','name' => 'HWB10','description' => 'Product to meet health and wellbeing needs'),
            array('id' => '17','name' => 'EF1','description' => 'Systems, vehicles and equipment'),
            array('id' => '18','name' => 'EF2','description' => 'Environments and buildings'),
            array('id' => '19','name' => 'EF3','description' => 'Transport and logistics'),
            array('id' => '20','name' => 'IK1','description' => 'Information processing'),
            array('id' => '21','name' => 'IK2','description' => 'Information collection and analysis'),
            array('id' => '22','name' => 'IK3','description' => 'Knowledge and information resources'),
            array('id' => '23','name' => 'G1','description' => 'Learning and development'),
            array('id' => '24','name' => 'G2','description' => 'Development and innovation'),
            array('id' => '25','name' => 'G3','description' => 'Procurement and commissioning'),
            array('id' => '26','name' => 'G4','description' => 'Finanacial management'),
            array('id' => '27','name' => 'G5','description' => 'Services and project management'),
            array('id' => '28','name' => 'G6','description' => 'People management'),
            array('id' => '29','name' => 'G7','description' => 'Capacity and capability'),
            array('id' => '30','name' => 'G8','description' => 'Public relations and marketing')
        );

            foreach($ksfs as $i) {
              DB::table('ksfs')->insert($i);
          }
    }
}
