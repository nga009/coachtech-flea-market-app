<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Favorite;
use App\Models\Category;
use App\Models\Comment;


class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 必要な情報が表示されるテスト
     * 
     * テスト内容：必要な情報が表示される
     * 
     * @test
     */
    public function 必要な情報が表示される()
    {
        // テスト準備: 詳細データを持つ商品を作成
        $seller = User::factory()->withProfile()->create();
        
        $category1 = Category::factory()->create(['name' => 'カテゴリ1']);
        $category2 = Category::factory()->create(['name' => 'カテゴリ2']);

        $item = Item::factory()->create([
            'item_image' => 'item_images/Armani+Mens+Clock.jpg',
            'condition' => 1,
            'name' => '腕時計',
            'brand' => 'Rolax',
            'description' => 'スタイリッシュなデザインのメンズ腕時計',
            'price' => 15000,
            'seller_id' => $seller->id,
            'is_sold' => false,

        ]);

        // カテゴリを関連付け
        $item->categories()->attach([$category1->id, $category2->id]);

        // いいねを作成
        $likeUsers = User::factory()->withProfile()->count(3)->create();
        foreach ($likeUsers as $user) {
            Favorite::factory()->create([
                'user_id' => $user->id,
                'item_id' => $item->id,
            ]);
        }

        // コメントを作成
        $commentUser1 = User::factory()
            ->withProfileData()
            ->create(['name' => 'コメント太郎']);
        $commentUser2 = User::factory()
            ->withProfileData()
            ->create(['name' => 'コメント花子']);

        $comment1 = Comment::factory()->create([
            'item_id' => $item->id,
            'user_id' => $commentUser1->id,
            'comment' => '素晴らしい商品ですね！',
        ]);

        $comment2 = Comment::factory()->create([
            'item_id' => $item->id,
            'user_id' => $commentUser2->id,
            'comment' => '状態はいかがですか？',
        ]);

        // テスト手順1: 商品詳細ページを開く
        $response = $this->get("/item/{$item->id}");

        // 期待挙動: すべての情報が商品詳細ページに表示されている
        $response->assertStatus(200);

        // 基本商品情報
        $response->assertSee('腕時計');
        $response->assertSee('¥15,000');
        $response->assertSee('スタイリッシュなデザインのメンズ腕時計');
        $response->assertSee('Rolax');

        // 商品の状態
        $response->assertSee('良好');

        // いいね数（3件）
        $response->assertSee('<span class="favorite-count">3</span>', false);

        // コメント数（2件）
        $response->assertSee('<span class="comment-count">2</span>', false);

        // カテゴリ情報
        $response->assertSee('カテゴリ1');
        $response->assertSee('カテゴリ2');

        // 商品画像
        $response->assertSee('item_images/Armani+Mens+Clock.jpg');

        // コメントしたユーザー情報とコメント内容
        $response->assertSee('コメント太郎');
        $response->assertSee('コメント花子');
        $response->assertSee('素晴らしい商品ですね！');
        $response->assertSee('状態はいかがですか？');

    }

    /**
     * 複数選択されたカテゴリが表示されているかテスト
     * 
     * テスト内容：複数選択されたカテゴリが表示されているか
     * 
     * @test
     */
    public function 複数選択されたカテゴリが表示されているか()
    {
        // テスト準備: 複数カテゴリを持つ商品を作成
        $seller = User::factory()->withProfile()->create();

        $category1 = Category::factory()->create(['name' => 'カテゴリ1']);
        $category2 = Category::factory()->create(['name' => 'カテゴリ2']);

        $item = Item::factory()->create([
            'name' => 'iPhone 15 Pro',
            'seller_id' => $seller->id,
        ]);

       // カテゴリを関連付け
        $item->categories()->attach([$category1->id, $category2->id]);

        // テスト手順1: 商品詳細ページを開く
        $response = $this->get("/item/{$item->id}");

        // 期待挙動: 複数選択されたカテゴリが商品詳細ページに表示されている
        $response->assertStatus(200);

        // 各カテゴリが表示されている
        $response->assertSee('カテゴリ1');
        $response->assertSee('カテゴリ2');
 
    }
}
