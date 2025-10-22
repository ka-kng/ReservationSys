<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // ログインページが正しく表示されるかを確認
    public function test_login_page_can_be_rendered()
    {
        // ログインページにアクセス
        $response = $this->get('/login');

        $response->assertStatus(200);

        // 正しいテンプレートが使われているか確認
        $response->assertViewIs('management.login');
    }

    // 正しいIDとパスワードでログインできるかを確認
    public function test_user_can_login_with_correct_credentials()
    {
        // ユーザーを作る
        $user = User::factory()->create([
            'login_id' => 'test',
            'password' => bcrypt($password = 'password')
        ]);

        // 作ったユーザーでログイン試行
        $response = $this->post('/login', [
            'login_id' => $user->login_id,
            'password' => $password,
        ]);

        // ログイン成功後、予約一覧ページにリダイレクトされるか確認
        $response->assertRedirect('/reservations/list');

        // ログイン済みか確認
        $this->assertAuthenticatedAs($user);
    }

    // ログアウトできるかを確認
    public function test_user_can_logout()
    {
        $user = User::factory()->create();

        // ユーザーとしてログイン
        $this->actingAs($user);

        // ログアウト実行
        $response = $this->post('logout');

        // ログアウト後、ログインページにリダイレクトされるか確認
        $response->assertRedirect('/login');

        // ログアウト済みか確認
        $this->assertGuest();
    }

    //  間違ったID・パスワードではログインできないかを確認
    public function test_user_cannot_login_with_incorrect_credentials()
    {
        // 正しいパスワードでユーザー作成
        $user = User::factory()->create([
            'login_id' => 'testuser',
            'password' => bcrypt('correct_password')
        ]);

        // 間違ったパスワードでログイン試行
        $response = $this->post('/login', [
            'login_id' => $user->login_id,
            'password' => 'wrong_password',
        ]);

        // レスポンスの状態コードが302かを確認
        $response->assertStatus(302);

        // リダイレクト先がトップページになっているかを確認
        $response->assertRedirect('/');

        // エラー情報がセッションに入っているか確認
        $response->assertSessionHasErrors();

        // ログインしていないか確認
        $this->assertGuest();
    }

    // ログインしていない人が予約一覧ページにアクセスできないかを確認
    public function test_guests_cannot_access_reservation_list()
    {
        // ゲストでアクセス
        $response = $this->get('/reservations/list');

        // ログインページにリダイレクトされるか確認
        $response->assertRedirect('/login');
    }
}
