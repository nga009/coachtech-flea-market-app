<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

   /**
     * ログアウト機能のテスト
     * 
     * テスト内容：ログアウトができる
     * 
     * @test
     */
    public function ログアウトができる()
    {
        // テスト手順1: ユーザーにログインをする
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        // ユーザーをログイン状態にする
        $this->actingAs($user);
        $this->assertAuthenticated();

        // プロフィール画面にアクセスできることを確認
        $response = $this->get('/mypage/profile');
        $response->assertStatus(200);

        // テスト手順2: ログアウトボタンを押す
        $response = $this->post('/logout');

        // 期待挙動: ログアウト処理が実行される
        $response->assertStatus(302);
        $response->assertRedirect('/');

        // ユーザーがログアウトしていることを確認
        $this->assertGuest();

    }

    /**
     * ログアウト後に保護されたページにアクセスできないことを確認
     * 
     * @test
     */
    public function ログアウト後は保護されたページにアクセスできない()
    {
        // ユーザーを作成してログイン
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user);

        // ログアウト実行
        $this->post('/logout');

        // 保護されたページ（出品）にアクセス
        $response = $this->get('/sell');

        // ログイン画面にリダイレクトされることを確認
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }
    
}
