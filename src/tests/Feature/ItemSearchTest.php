<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Favorite;

class ItemSearchTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 商品名で部分一致検索ができるテスト
     * 
     * テスト内容：「商品名」で部分一致検索ができる
     * 
     * @test
     */
    public function 商品名で部分一致検索ができる()
    {
        // テスト準備: ログインユーザーと出品者を作成
        $loginUser = User::factory()->withProfile()->create();

        // 検索対象商品を作成
        $item1 = Item::factory()->create([
            'name' => 'iPhone 15 Pro',
        ]);

        $item2 = Item::factory()->create([
            'name' => 'iPhone 14',
        ]);

        $item3 = Item::factory()->create([
            'name' => 'Android スマートフォン',
        ]);

        $item4 = Item::factory()->create([
            'name' => 'MacBook Pro',
        ]);

        $this->actingAs($loginUser);

        // テスト手順1: 検索欄にキーワードを入力
        // テスト手順2: 検索ボタンを押す
        $response = $this->get('/?keyword=iPhone');

        // 期待挙動: 部分一致する商品が表示される
        $response->assertStatus(200);

        // iPhoneを含む商品が表示される
        $response->assertSee('iPhone 15 Pro');
        $response->assertSee('iPhone 14');

        // iPhoneを含まない商品は表示されない
        $response->assertDontSee('Android スマートフォン');
        $response->assertDontSee('MacBook Pro');

        // 検索フォームにキーワードが入力されている
        $this->assertEquals('iPhone', $response->viewData('keyword'));
    }

    /**
     * 検索状態がマイリストでも保持されているテスト
     * 
     * テスト内容：検索状態がマイリストでも保持されている
     * 
     * @test
     */
    public function 検索状態がマイリストでも保持されている()
    {
        // テスト準備: ユーザーと商品、いいねを作成
        $loginUser = User::factory()->withProfile()->create();

        $iphoneItem = Item::factory()->create([
            'name' => 'iPhone 15',
        ]);

        $androidItem = Item::factory()->create([
            'name' => 'Android Phone',
        ]);

        // 両方の商品をいいね
        Favorite::factory()->create([
            'user_id' => $loginUser->id,
            'item_id' => $iphoneItem->id,
        ]);

        Favorite::factory()->create([
            'user_id' => $loginUser->id,
            'item_id' => $androidItem->id,
        ]);

        $this->actingAs($loginUser);

        // テスト手順1: ホームページで商品を検索
        $response = $this->get('/?keyword=iPhone');

        // テスト手順2: 検索結果が表示される
        $response->assertStatus(200);
        $response->assertSee('iPhone 15');
        $response->assertDontSee('Android Phone');
        $this->assertEquals('iPhone', $response->viewData('keyword'));

        // テスト手順3: マイリストページに遷移
        $response = $this->get('/?tab=mylist&keyword=iPhone');

        // 期待挙動: 検索キーワードが保持されている
        $response->assertStatus(200);
        $this->assertEquals('iPhone', $response->viewData('keyword'));

        // お気に入りの中でも検索が適用される
        $response->assertSee('iPhone 15');
        $response->assertDontSee('Android Phone');
    }
}
