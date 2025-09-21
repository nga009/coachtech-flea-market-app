<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class UserRegisterValidationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 名前が入力されていない場合のバリデーションテスト
     * 
     * テスト内容：名前が入力されていない場合、バリデーションメッセージが表示される
     * 
     * @test
     */
    public function 名前が未入力の場合バリデーションメッセージが表示される()
    {
        // テスト手順1: 会員登録ページを開く
        $response = $this->get('/register');
        $response->assertStatus(200);

        // テスト手順2: 名前を入力せずに他の必要項目を入力
        $formData = [
            'name' => '', // 名前を空にする
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // テスト手順3: 登録ボタンを押す（POSTリクエスト送信）
        $response = $this->post('/register', $formData);

        // 期待挙動: 「お名前を入力してください」というメッセージが表示される
        $response->assertSessionHasErrors([
            'name' => 'お名前を入力してください'
        ]);

        // ユーザーが作成されていないことを確認
        $this->assertDatabaseMissing('users', [
            'email' => 'test@example.com'
        ]);
    }

    /**
     * 名前が20文字を超える場合のバリデーションテスト
     * 
     * @test
     */
    public function 名前が20文字を超える場合バリデーションメッセージが表示される()
    {
        $formData = [
            'name' => str_repeat('あ', 21), // 21文字の名前
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post('/register', $formData);

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'name' => 'お名前は20文字以下で入力してください'
        ]);
    }

    /**
     * メールアドレスが未入力の場合のバリデーションテスト
     * 
     * @test
     */
    public function メールアドレスが未入力の場合バリデーションメッセージが表示される()
    {
        $formData = [
            'name' => 'テストユーザー',
            'email' => '', // メールアドレスを空にする
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post('/register', $formData);

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください'
        ]);
    }

    /**
     * メールアドレスの形式が正しくない場合のバリデーションテスト
     * 
     * @test
     */
    public function メールアドレスの形式が正しくない場合バリデーションメッセージが表示される()
    {
        $formData = [
            'name' => 'テストユーザー',
            'email' => 'invalid-email-format', // 不正なメール形式
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post('/register', $formData);

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'email' => 'メールアドレスはメール形式で入力してください'
        ]);
    }

    /**
     * パスワードが未入力の場合のバリデーションテスト
     * 
     * @test
     */
    public function パスワードが未入力の場合バリデーションメッセージが表示される()
    {
        $formData = [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => '', // パスワードを空にする
            'password_confirmation' => 'password123',
        ];

        $response = $this->post('/register', $formData);

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください'
        ]);
    }

    /**
     * パスワードが8文字未満の場合のバリデーションテスト
     * 
     * @test
     */
    public function パスワードが8文字未満の場合バリデーションメッセージが表示される()
    {
        $formData = [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => '1234567', // 7文字のパスワード
            'password_confirmation' => '1234567',
        ];

        $response = $this->post('/register', $formData);

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'password' => 'パスワードは8文字以上で入力してください'
        ]);
    }

    /**
     * 確認用パスワードが未入力の場合のバリデーションテスト
     * 
     * @test
     */
    public function 確認用パスワードが未入力の場合バリデーションメッセージが表示される()
    {
        $formData = [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => '', // 確認用パスワードを空にする
        ];

        $response = $this->post('/register', $formData);

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'password_confirmation' => '確認用パスワードを入力してください'
        ]);
    }

    /**
     * パスワードと確認用パスワードが一致しない場合のバリデーションテスト
     * 
     * @test
     */
    public function パスワードと確認用パスワードが一致しない場合バリデーションメッセージが表示される()
    {
        $formData = [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different_password', // 異なるパスワード
        ];

        $response = $this->post('/register', $formData);

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'password_confirmation' => 'パスワードと一致しません'
        ]);
    }

    /**
     * 既に登録されているメールアドレスの場合のバリデーションテスト
     * 
     * @test
     */
/*    public function 既に登録されているメールアドレスの場合バリデーションメッセージが表示される()
    {
        // 事前にユーザーを作成
        User::factory()->create([
            'email' => 'existing@example.com'
        ]);

        $formData = [
            'name' => 'テストユーザー',
            'email' => 'existing@example.com', // 既存のメールアドレス
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post('/register', $formData);

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'email' => 'このメールアドレスは既に登録されています'
        ]);

    }
*/

    /**
     * すべての項目が正しく入力されている場合の正常テスト
     * 
     * @test
     */
    public function すべての項目が正しく入力されている場合登録が成功する()
    {
        $formData = [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post('/register', $formData);

        // 登録成功後はメール認証画面へリダイレクト
        $response->assertRedirect('/');

        // ユーザーがデータベースに作成されていることを確認
        $this->assertDatabaseHas('users', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com'
        ]);

        // ユーザーがログイン状態になっていることを確認
        $this->assertAuthenticated();
    }
}
