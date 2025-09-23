<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;

class ProfileEditTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 過去設定された値が初期値としてされていること
     * 
     * テスト内容：過去設定された値が初期値としてされていること（プロフィール画像、ユーザー名、郵便番号、住所）
     * 
     * @test
     */
    public function 過去設定された値が初期値としてされていること()
    {
        // テスト準備：完全なプロフィール情報を持つユーザーを作成
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'email' => 'test-user@example.com',
            'email_verified_at' => now(),
        ]);

        // プロフィール情報を作成
        $profile = Profile::factory()->create([
            'user_id' => $user->id,
            'name' => '山田太郎',
            'postcode' => '123-4567',
            'address' => '東京都渋谷区神南1-2-3',
            'building' => 'テストマンション101号室',
            'profile_image' => 'profiles/test_profile_image.jpg',
        ]);

        // テスト手順1: ユーザーにログインする
        $this->actingAs($user);

        // テスト手順2: プロフィールページを開く
        $response = $this->get('/mypage/profile');

        // 期待挙動: 各項目の初期値が正しく表示されている
        $response->assertStatus(200);
        $response->assertSee('プロフィール設定');

        // 氏名の初期値確認  
        $response->assertSee('value="山田太郎"', false);
        
        // 郵便番号の初期値確認
        $response->assertSee('value="123-4567"', false);
        
        // 住所の初期値確認
        $response->assertSee('value="東京都渋谷区神南1-2-3"', false);

        // 建物の初期値確認
        $response->assertSee('value="テストマンション101号室"', false);
        
        // プロフィール画像の表示確認
        $response->assertSee('profiles/test_profile_image.jpg');

    }
}
