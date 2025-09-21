<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Favorite;

class MyListTest extends TestCase
{
     use RefreshDatabase;

    /**
     * いいねした商品だけが表示される
     * 
     * テスト内容：いいねした商品だけが表示される
     * 
     * @test
     */
    public function いいねした商品だけが表示される()
    {
        // テスト準備: ユーザーと商品を作成
        $loginUser = User::factory()
                ->withProfile()
                ->create([
                    'name' => 'ログインユーザー',
                ]);
        
        $otherUser = User::factory()
                ->withProfile()
                ->create(['name' => '他のユーザー']);
        
        // ログインユーザーがいいねした商品
        $myItem1 = Item::factory()->create([
            'name' => 'いいねした商品1',
        ]);
        
        $myItem2 = Item::factory()->create([
            'name' => 'いいねした商品2',
        ]);
        
        // いいねしてない商品
        $otherItem1 = Item::factory()->create([
            'name' => 'いいねしてない商品1',
        ]);
        
        $otherItem2 = Item::factory()->create([
            'name' => 'いいねしてない商品2',
        ]);


        // いいねを作成
        Favorite::factory()->create([
            'user_id' => $loginUser->id,
            'item_id' => $myItem1->id,
        ]);

        Favorite::factory()->create([
            'user_id' => $loginUser->id,
            'item_id' => $myItem2->id,
        ]);        


        // テスト手順1: ユーザーにログインをする
        $this->actingAs($loginUser);

        // テスト手順2: マイリストを開く
        $response = $this->get('/?tab=mylist');

        // 期待挙動: いいねした商品だけが表示される
        $response->assertStatus(200);
        $response->assertSee('マイリスト');
        
        // いいねした商品は表示される
        $response->assertSee('いいねした商品1');
        $response->assertSee('いいねした商品2');

        // いいねしていない商品は表示されない
        $response->assertDontSee('いいねしていない商品1');
        $response->assertDontSee('いいねしていない商品2');
    }    

    /**
     * 購入済み商品は「Sold」と表示される
     * 
     * テスト内容：購入済み商品は「Sold」と表示される
     * 
     * @test
     */
    public function 購入済み商品は「Sold」と表示される()
    {
        // テスト準備: ユーザーと商品を作成
        $loginUser = User::factory()
                ->withProfile()
                ->create([
                    'name' => 'ログインユーザー',
                ]);
        
        // ログインユーザーがいいねした商品
        $myItem = Item::factory()->create([
            'name' => 'いいねした商品',
            'is_sold' => false,
        ]);
        
        $soldMyItem = Item::factory()->create([
            'name' => '購入済みのいいねした商品',
            'is_sold' => true,
        ]);

        // いいねを作成
        Favorite::factory()->create([
            'user_id' => $loginUser->id,
            'item_id' => $myItem->id,
        ]);

        Favorite::factory()->create([
            'user_id' => $loginUser->id,
            'item_id' => $soldMyItem->id,
        ]);        


        // テスト手順1: ユーザーにログインをする
        $this->actingAs($loginUser);

        // テスト手順2: マイリストを開く
        $response = $this->get('/?tab=mylist');

        // 期待挙動: 購入済み商品は「Sold」と表示される
        $response->assertStatus(200);
        $response->assertSee('マイリスト');
        
        // 購入未済商品は「Sold」が表示されない
        $response->assertSeeInOrder([
            'いいねした商品',
            '購入済みのいいねした商品',  // 別の商品が間に入る
            'Sold'
        ]);

        // 購入済み商品には「Sold」が表示される
        $response->assertSeeInOrder([
            '購入済みのいいねした商品',
            'Sold'
        ]);        
    }    

    /**
     * 未認証の場合は何も表示されないテスト
     * 
     * テスト内容：未認証の場合は何も表示されない
     * 
     * @test
     */
    public function 未認証の場合は何も表示されない()
    {
        // テスト準備: ユーザーと商品を作成
        $loginUser = User::factory()
                ->withProfile()
                ->create([
                    'name' => 'ログインユーザー',
                ]);
        
        // ログインユーザーがいいねした商品
        $myItem = Item::factory()->create([
            'name' => 'いいねした商品',
            'is_sold' => false,
        ]);
        
        // いいねを作成
        Favorite::factory()->create([
            'user_id' => $loginUser->id,
            'item_id' => $myItem->id,
        ]);

        // テスト手順1: マイリストページを開く（未認証状態）
        $response = $this->get('/?tab=mylist');

        // 期待挙動: 何も表示されない
        $response->assertStatus(200);
        $response->assertSee('マイリスト');

        // 商品は表示されない
        $response->assertDontSee('いいねした商品');

    }
}
