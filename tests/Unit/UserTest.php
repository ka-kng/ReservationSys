<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
{

    use RefreshDatabase;

    // fillable に設定されているカラムが正しいか確認
    public function test_user_expected_fillable_fields()
    {
        $user = new User();

        // fillableは「まとめて登録できる項目」を指定する
        $this->assertEquals(
            ['name', 'login_id', 'password'],
            $user->getFillable()
        );
    }

    // password と remember_token が隠される設定になっているか確認
    public function test_user_hides_password_and_remenber_token()
    {
        $user = new User();

        $this->assertEquals(
            ['password', 'remember_token'],
            $user->getHidden()
        );
    }

    // ユーザーがDBに作成できるか確認
    public function test_user_can_be_created_in_database()
    {
        // ユーザーを作成（パスワードはハッシュ化）
        $user = User::factory()->create([
            'name' => 'Test User',
            'login_id' => 'test123',
            'password' => Hash::make('password'),
        ]);

        // データベースに作成されたか確認
        $this->assertDatabaseHas('users', [
            'login_id' => 'test123',
        ]);
    }
}
