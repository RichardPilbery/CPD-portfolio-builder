<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = array(
            array('id' => '1','title' => 'Paramedic'),
            array('id' => '2','title' => 'Clinical Supervisor'),
            array('id' => '3','title' => 'Other'),
            array('id' => '4','title' => 'ECP - paramedic'),
            array('id' => '5','title' => 'Ambulance Technician'),
            array('id' => '6','title' => 'Clinical Tutor'),
            array('id' => '7','title' => 'Clinical Team Leader'),
            array('id' => '8','title' => 'Student Paramedic'),
            array('id' => '9','title' => 'Advanced EMT'),
            array('id' => '10','title' => 'Driving Tutor'),
            array('id' => '11','title' => 'Paramedic practitioner'),
            array('id' => '12','title' => 'HART operative'),
            array('id' => '13','title' => 'Clinical Advisor'),
            array('id' => '14','title' => 'ECA'),
            array('id' => '15','title' => 'ECP - nurse'),
            array('id' => '16','title' => 'HEMS Aircrew Paramedic'),
            array('id' => '17','title' => 'Duty Operations Manager'),
            array('id' => '18','title' => 'Advanced Practitioner'),
            array('id' => '19','title' => 'Critical Care Paramedic'),
            array('id' => '20','title' => 'Community first responder'),
            array('id' => '21','title' => 'Senior Paramedic'),
            array('id' => '22','title' => 'Senior EMT')
          );

          foreach($roles as $i) {
            DB::table('roles')->insert($i);
        }
    }
}
