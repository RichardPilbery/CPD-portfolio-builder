<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClfSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clfs = array(
            array('id' => '1','name' => 'L1.1','domain' => 'Demonstrating personal qualities','element' => 'Developing self awareness'),
            array('id' => '2','name' => 'L1.2','domain' => 'Demonstrating personal qualities','element' => 'Managing yourself'),
            array('id' => '3','name' => 'L1.3','domain' => 'Demonstrating personal qualities','element' => 'Continuing personal development'),
            array('id' => '4','name' => 'L1.4','domain' => 'Demonstrating personal qualities','element' => 'Acting with integrity'),
            array('id' => '5','name' => 'L2.1','domain' => 'Working with others','element' => 'Developing networks'),
            array('id' => '6','name' => 'L2.2','domain' => 'Working with others','element' => 'Building and maintaining relationships'),
            array('id' => '7','name' => 'L2.3','domain' => 'Working with others','element' => 'Encouraging contribution'),
            array('id' => '8','name' => 'L2.4','domain' => 'Working with others','element' => 'Working within teams'),
            array('id' => '9','name' => 'L3.1','domain' => 'Managing services','element' => 'Planning'),
            array('id' => '10','name' => 'L3.2','domain' => 'Managing services','element' => 'Managing resources'),
            array('id' => '11','name' => 'L3.3','domain' => 'Managing services','element' => 'Managing people'),
            array('id' => '12','name' => 'L3.4','domain' => 'Managing services','element' => 'Managing performance'),
            array('id' => '13','name' => 'L4.1','domain' => 'Improving services','element' => 'Ensuring patient safety'),
            array('id' => '14','name' => 'L4.2','domain' => 'Improving services','element' => 'Critically evaluating'),
            array('id' => '15','name' => 'L4.3','domain' => 'Improving services','element' => 'Encouraging and improving innovation'),
            array('id' => '16','name' => 'L4.4','domain' => 'Improving services','element' => 'Facilitating transformation'),
            array('id' => '17','name' => 'L5.1','domain' => 'Setting direction','element' => 'Identifying the contexts for change'),
            array('id' => '18','name' => 'L5.2','domain' => 'Setting direction','element' => 'Applying knowledge and evidence'),
            array('id' => '19','name' => 'L5.3','domain' => 'Setting direction','element' => 'Making decisions'),
            array('id' => '20','name' => 'L5.4','domain' => 'Setting direction','element' => 'Evaluating impact')
          );


          foreach($clfs as $i) {
              DB::table('clfs')->insert($i);
          }
    }
}
