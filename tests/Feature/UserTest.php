<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Portfolio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_can_change_their_password()
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();

        $this->patch('user/'.$user->id, [
            'id' => $user->id,
            'email' => 'test_email',
            'password' => 'changedpassword'
        ]);

        $this->get('/logout');

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'changedpassword',
        ]);

        $this->assertAuthenticated();

    }

    public function test_user_cannot_change_another_users_password()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();

        $response1 = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();

        $response = $this->patch('user/'.$user2->id, [
            'name' => $user->name,
            'service_id' => $user->service_id,
            'password' => 'changedpassword',
            'email' => 'random@example.com',
            'id' => $user2->id
        ]);
        //dd($response);
        $response->assertStatus(403);

    }

    public function test_admin_user_can_change_another_users_password()
    {
        $oldPassword = 'password123';

        $user = User::factory()->create(['password' => Hash::make($oldPassword)]);
        $admin = User::factory()->create(['admin' => 1]);

        $response1 = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();

        $response = $this->patch('user/'.$user->id, [
            'name' => $user->name,
            'service_id' => $user->service_id,
            'password' => 'changedpassword',
            'email' => $user->email,
            'id' => $user->id,
        ]);

        // Hashes are a bit of a nightmare as they keep changing
        // So we are going to test by logging in as original user
        // with new password

        $this->get('/logout');

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'changedpassword',
        ]);

        $this->assertAuthenticated();


    }


    public function test_admin_user_can_change_their_own_password()
    {
        $oldPassword = 'password123';

        $user = User::factory()->create(['password' => Hash::make($oldPassword), 'admin' => 1]);

        $response1 = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();

        $response = $this->patch('user/'.$user->id, [
            'name' => $user->name,
            'service_id' => $user->service_id,
            'password' => 'changedpassword',
            'email' => $user->email,
            'id' => $user->id,
        ]);

        // Hashes are a bit of a nightmare as they keep changing
        // So we are going to test by logging in as original user
        // with new password

        $this->get('/logout');

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'changedpassword',
        ]);

        $this->assertAuthenticated();
    }

    public function test_admin_can_delete_a_user_and_portfolio_entries() {
        // Create User
        $user = User::factory()->create();

        $portfolio = Portfolio::factory()->create(['user_id'=>$user->id]);
        $portfolio2 = Portfolio::factory()->create(['user_id'=>$user->id]);

        $this->assertDatabaseHas('portfolios', [
            'user_id' => $user->id,
            'title' => $portfolio->title
        ]);

        $this->assertDatabaseHas('portfolios', [
            'user_id' => $user->id,
            'title' => $portfolio2->title
        ]);

        // Create admin user and log in
        $user2 = User::factory()->create(['admin'=>1]);

        $response1 = $this->post('/login', [
            'email' => $user2->email,
            'password' => 'password123',
        ]);
        $this->assertAuthenticated();

        $response2 = $this->delete('/user/'.$user->id);
        $response2->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('users', ['id'=>$user->id]);
        $this->assertDatabaseMissing('portfolios', ['user_id'=>$user->id]);

    }
}
