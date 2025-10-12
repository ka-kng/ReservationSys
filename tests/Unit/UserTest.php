<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function user_expected_fillable_fields()
    {
        $user = new User();

        $this->assertEquals(
            ['name', 'login_id', 'password'],
            $user->getFillable()
        );
    }

    /** @test */
    public function user_hides_password_and_remenber_token()
    {
        $user = new User();

        $this->assertEquals(
            ['password', 'remember_token'],
            $user->getHidden()
        );
    }

    /** @test */
    public function user_can_be_created_in_database()
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'login_id' => 'test123',
            'password' => Hash::make('password'),
        ]);

        $this->assertDatabaseHas('users', [
            'login_id' => 'test123',
        ]);
    }
}
