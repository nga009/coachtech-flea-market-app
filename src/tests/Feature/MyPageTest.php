<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Profile;
use App\Models\Purchase;

class MyPageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 必要な情報が取得できる（プロフィール画像、ユーザー名、出品した商品一覧、購入した商品一覧）
     * 
     * テスト内容：必要な情報が取得できる（プロフィール画像、ユーザー名、出品した商品一覧、購入した商品一覧）
     * 
     * @test
     */
    public function 必要な情報が取得できる()
    {
        // テスト準備
        $user = User::factory()->create([
            'name' => 'testuser123',
            'email' => 'testuser@example.com',
        ]);

        // プロフィール情報を作成
        $profile = Profile::factory()->create([
            'user_id' => $user->id,
            'name' => 'テストユーザー太郎',
            'address' => '東京都渋谷区神南1-2-3',
            'profile_image' => 'profiles/user_profile_image.jpg',
        ]);

        // 他のユーザーを作成
        $buyer = User::factory()->create();
        Profile::factory()->create([
            'user_id' => $buyer->id,
            'name' => '購入者花子',
        ]);

        $seller = User::factory()->create();
        Profile::factory()->create([
            'user_id' => $seller->id,
            'name' => '出品者次郎',
        ]);

        // 出品した商品を作成
        $soldItem1 = Item::factory()->create([
            'name' => '出品商品1（売り切れ）',
            'price' => 15000,
            'seller_id' => $user->id,
            'is_sold' => true,
        ]);

        $soldItem2 = Item::factory()->create([
            'name' => '出品商品2（販売中）',
            'price' => 25000,
            'seller_id' => $user->id,
            'is_sold' => false,
        ]);

        // 出品商品1の購入履歴を作成
        Purchase::factory()->create([
            'item_id' => $soldItem1->id,
            'buyer_id' => $buyer->id,
        ]);

        // 購入した商品を作成
        $purchasedItem1 = Item::factory()->create([
            'name' => '購入商品1',
            'price' => 12000,
            'seller_id' => $seller->id,
            'is_sold' => true,
        ]);

        $purchasedItem2 = Item::factory()->create([
            'name' => '購入商品2',
            'price' => 8000,
            'seller_id' => $seller->id,
            'is_sold' => true,
        ]);

        // 購入履歴を作成
        Purchase::factory()->create([
            'item_id' => $purchasedItem1->id,
            'buyer_id' => $user->id,
        ]);

        Purchase::factory()->create([
            'item_id' => $purchasedItem2->id,
            'buyer_id' => $user->id,
        ]);

        // テスト手順1: ユーザーにログインする
        $this->actingAs($user);

        // テスト手順2: プロフィールページを開く
        $response = $this->get('/mypage');

        // 期待挙動: プロフィール画像、ユーザー名、出品した商品一覧、購入した商品一覧が正しく表示される
        $response->assertStatus(200);

        // === プロフィール画像の表示確認 ===
        $response->assertSee('profiles/user_profile_image.jpg');

        // === ユーザー名の表示確認 ===
        $response->assertSee('testuser123'); // ユーザー名

        // === タブの表示確認 ===
        $response->assertSee('購入した商品');
        $response->assertSee('出品した商品');

        // === 購入した商品一覧の表示確認 ===
        $response = $this->get('/mypage?page=buy');
        
        // 購入した商品の詳細情報
        $response->assertSee('購入商品1');
        $response->assertSee('購入商品2');
        
        // === 出品した商品一覧の表示確認 ===
        $response = $this->get('/mypage?page=sell');
        
        // 出品した商品の詳細情報
        $response->assertSee('出品商品1（売り切れ）');
        $response->assertSee('出品商品2（販売中）');
        
        // 売り切れ商品にはSoldラベル
        $response->assertSee('Sold');
    }
}
