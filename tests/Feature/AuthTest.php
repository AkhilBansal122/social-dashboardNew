<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_loads(): void
    {
        $this->get('/login')->assertStatus(200);
    }

    public function test_register_page_loads(): void
    {
        $this->get('/register')->assertStatus(200);
    }

    public function test_user_can_register(): void
    {
        $this->post('/register', [
            'name'                  => 'Test User',
            'email'                 => 'test@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect('/dashboard');

        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create();
        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect('/dashboard');
    }

    public function test_wrong_password_rejected(): void
    {
        $user = User::factory()->create();
        $this->post('/login', ['email' => $user->email, 'password' => 'wrong'])
            ->assertSessionHasErrors('email');
    }

    public function test_dashboard_redirects_guests(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_authenticated_user_cannot_see_login(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/login')->assertRedirect('/dashboard');
    }
}
