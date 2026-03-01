<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Portfolio;
use App\Models\Swot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SwotTest extends TestCase
{
    public function test_check_guest_user_cannot_create_a_swot_entry() {
        $response = $this->get('/swot/');
        $response->assertRedirect('/login');

        $response = $this->post('/swot', [
            'strength' => 'I am strong',
            'weakness' => 'I am weak',
            'opportunity' => 'There are ops',
            'threat' => 'There are threats'
        ]);

        $response->assertRedirect('/login');
    }

    public function test_create_a_swot_entry()
    {
        $user = User::factory()->create();
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();

        $response = $this->post('/swot', [
            'strength' => 'I am strong',
            'weakness' => 'I am weak',
            'opportunity' => 'There are ops',
            'threat' => 'There are threats'
        ]);

        $response->assertSessionHasNoErrors();
        $portfolio = Portfolio::latest()->first();

        $this->assertDatabaseHas('swots', [
            'strength' => 'I am strong',
            'portfolio_id' => $portfolio->id
        ]);
    }

    public function test_check_user_can_update_swot_entry() {

        $user = User::factory()->create();
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);
        $this->assertAuthenticated();

        $response = $this->post('/swot', [
            'strength' => 'I am strong',
            'weakness' => 'I am weak',
            'opportunity' => 'There are ops',
            'threat' => 'There are threats'
        ]);

        $portfolio = Portfolio::latest()->first();
        $swot = Swot::latest()->first();        

        $swot_strength = $swot->strength;
        $swot_weakness = $swot->weakness;

        $this->assertDatabaseHas('swots', [
            'portfolio_id' => $portfolio->id,
            'strength' => $swot_strength,
            'weakness' => $swot_weakness
        ]);

        $newP = $portfolio;

        $newP->strength = "Updated strength";
        $newP->weakness = "Updated weakness";
        $newP->opportunity = "Updated opps";
        $newP->threat = $swot->threat;
        // $newP->updated_at = '2023-01-01T13:00';
        $newP->start = '2022-01-01T12:30';
        $newP->end =  '2022-01-01T13:00';

        $response = $this->patch('/portfolio/'.$portfolio->id, $newP->toArray());
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('swots', [
            'portfolio_id' => $newP->id,
            'strength' => $newP->strength,
            'weakness' => $newP->weakness
        ]);

    }

    public function test_swot_is_deleted_when_portfolio_entry_is_deleted() {
        $user = User::factory()->create();
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();

        $portfolio = Portfolio::factory()->create(['user_id'=>$user->id]);
        $swot = Swot::factory()->create(['portfolio_id'=>$portfolio->id]);

        $swot_strength = $swot->strength;

        $response = $this->delete('/portfolio/'.$portfolio->id);

        $this->assertDatabaseMissing('portfolios', [
            'id' => $portfolio->id
        ]);

        $this->assertDatabaseMissing('swots', [
            'portfolio_id' => $portfolio->id,
            'strength' => $swot_strength
        ]);

    }
}
