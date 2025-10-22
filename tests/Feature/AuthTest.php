<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_can_be_rendered()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertViewIs('management.login');
    }

    public function test_user_can_login_with_correct_credentials()
    {
        $user = User::factory()->create([
            'login_id' => 'test',
            'password' => bcrypt($password = 'password')
        ]);

        $response = $this->post('/login', [
            'login_id' => $user->login_id,
            'password' => $password,
        ]);
        $response->assertRedirect('/reservations/list');

        $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_logout()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->post('logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_user_cannot_login_with_incorrect_credentials()
    {
        $user = User::factory()->create([
            'login_id' => 'testuser',
            'password' => bcrypt('correct_password')
        ]);

        $response = $this->post('/login', [
            'login_id' => $user->login_id,
            'password' => 'wrong_password',
        ]);

        // Laravel Breeze デフォルトでは失敗時に / にリダイレクト
        $response->assertStatus(302);
        $response->assertRedirect('/');

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    public function test_guests_cannot_access_reservation_list()
    {
        $response = $this->get('/reservations/list');

        // authミドルウェアによる保護で /login にリダイレクト
        $response->assertRedirect('/login');
    }
}
