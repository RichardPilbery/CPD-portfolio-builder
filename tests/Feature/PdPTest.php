<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Pdp;
use App\Models\User;

class PdPTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_create_a_pdp_entry()
    {
        $user = User::factory()->create();
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();

        $response = $this->post('/pdp', [
            'objective' => 'Objective',
            'activity' => 'Activity',
            'measure' => 'Measure',
            'support' => 'Support',
            'barriers' => 'Barriers',
            'finishdate' => '2023-12-12'
        ]);

        //dd($response);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('pdps', [
            'user_id' => $user->id,
            'objective' => 'Objective',
            'activity' => 'Activity' 
        ]);
    }

    public function test_check_user_can_update_a_pdp_entry() {

        $user = User::factory()->create();
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);
        $this->assertAuthenticated();

        $pdp = Pdp::factory()->create(['user_id'=>$user->id]);

        $test_objective = $pdp->objective;

        $this->assertDatabaseHas('pdps', [
            'user_id' => $user->id,
            'objective' => $test_objective
        ]);

        $newP = $pdp;

        $newP->objective = "Updated objective";

        $response = $this->patch('/pdp/'.$pdp->id, $newP->toArray());

        $this->assertDatabaseHas('pdps', [
            'objective' => $newP->objective,
            'user_id' => $user->id,
        ]);

        

    }

    public function test_check_user_can_delete_a_pdp_entry() {

        $user = User::factory()->create();
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);
        $this->assertAuthenticated();

        $pdp = Pdp::factory()->create(['user_id'=>$user->id]);

        $test_objective = $pdp->objective;

        $this->assertDatabaseHas('pdps', [
            'user_id' => $user->id,
            'objective' => $test_objective
        ]);

        $response = $this->delete('/pdp/'.$pdp->id);

        $this->assertDatabaseMissing('pdps', [
            'objective' => $test_objective,
            'user_id' => $user->id,
        ]);

        

    }


}
