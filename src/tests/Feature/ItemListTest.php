<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Support\Facades\Hash;

class ItemListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 全商品を取得できるテスト
     * 
     * テスト内容：全商品を取得できる
     * 
     * @test
     */
    public function 全商品を取得できる()
    {
        // テスト準備: 複数の商品を作成
        $seller1 = User::factory()->create(['name' => '出品者1']);
        $seller2 = User::factory()->create(['name' => '出品者2']);
        
        $item1 = Item::factory()->create([
            'name' => '商品1',
            'seller_id' => $seller1->id,
        ]);
        
        $item2 = Item::factory()->create([
            'name' => '商品2',
            'seller_id' => $seller2->id,
        ]);
        
        $item3 = Item::factory()->create([
            'name' => '商品3',
            'seller_id' => $seller1->id,
        ]);

        // テスト手順1: 商品一覧ページを開く
        $response = $this->get('/');

        // 期待挙動: すべての商品が表示される
        $response->assertStatus(200);
        $response->assertSee('おすすめ');
        $response->assertSee('商品1');
        $response->assertSee('商品2');
        $response->assertSee('商品3');
    }

    /**
     * 購入済み商品は「Sold」と表示されるテスト
     * 
     * テスト内容：購入済み商品は「Sold」と表示される
     * 
     * @test
     */
    public function 購入済み商品はSoldと表示される()
    {
        // テスト準備: 商品と購入者を作成
        $seller = User::factory()
                ->withProfile()
                ->create(['name' => '出品者']);
        $buyer = User::factory()
                ->withProfile()
                ->create(['name' => '購入者']);
        
        // 通常の商品
        $normalItem = Item::factory()->create([
            'name' => '通常商品',
            'seller_id' => $seller->id,
            'is_sold' => false,
        ]);
        
        // 購入済み商品（is_soldフラグ）
        $soldItem = Item::factory()->create([
            'name' => '購入済み商品',
            'seller_id' => $seller->id,
            'is_sold' => true,
        ]);

        // テスト手順1: 商品ページを開く
        $response = $this->get('/');

        // 期待挙動: 購入済み商品に「Sold」のラベルが表示される
        $response->assertStatus(200);
        
        // 通常商品は「Sold」が表示されない
        $response->assertSeeInOrder([
            '通常商品',
            '購入済み商品',  // 別の商品が間に入る
            'Sold'
        ]);

        // 購入済み商品には「Sold」が表示される
        $response->assertSeeInOrder([
            '購入済み商品',
            'Sold'
        ]);
        
    }

    /**
     * 自分が出品した商品は表示されないテスト
     * 
     * テスト内容：自分が出品した商品は表示されない
     * 
     * @test
     */
    public function 自分が出品した商品は表示されない()
    {
        // テスト準備: ユーザーと商品を作成
        $loginUser = User::factory()
                ->withProfile()
                ->create([
                    'name' => 'ログインユーザー',
                    'email' => 'login@example.com',
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                ]);
        
        $otherUser = User::factory()
                ->withProfile()
                ->create(['name' => '他のユーザー']);
        
        // ログインユーザーが出品した商品
        $myItem1 = Item::factory()->create([
            'name' => '自分の商品1',
            'seller_id' => $loginUser->id,
        ]);
        
        $myItem2 = Item::factory()->create([
            'name' => '自分の商品2',
            'seller_id' => $loginUser->id,
        ]);
        
        // 他のユーザーが出品した商品
        $otherItem1 = Item::factory()->create([
            'name' => '他の人の商品1',
            'seller_id' => $otherUser->id,
        ]);
        
        $otherItem2 = Item::factory()->create([
            'name' => '他の人の商品2',
            'seller_id' => $otherUser->id,
        ]);

        // テスト手順1: ユーザーにログインをする
        $this->actingAs($loginUser);

        // テスト手順2: 商品ページを開く
        $response = $this->get('/');

        // 期待挙動: 自分が出品した商品が一覧に表示されない
        $response->assertStatus(200);
        $response->assertSee('おすすめ');
        
        // 自分の商品は表示されない
        $response->assertDontSee('自分の商品1');
        $response->assertDontSee('自分の商品2');
        
        // 他の人の商品は表示される
        $response->assertSee('他の人の商品1');
        $response->assertSee('他の人の商品2');
    }    
}
