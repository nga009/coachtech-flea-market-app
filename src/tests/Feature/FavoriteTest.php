<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Favorite;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * いいねアイコンを押下することによって、いいねした商品として登録することができる
     * 
     * テスト内容：
     * いいねアイコンを押下することによって、いいねした商品として登録することができる
     * 追加済みのアイコンは色が変化する
     * 
     * @test
     */
    public function いいねアイコンを押下することによっていいねした商品として登録することができる()
    {
        // テスト準備
        $loginUser = User::factory()->withProfile()->create();
        
        $item = Item::factory()->create([
            'name' => 'いいねテスト商品',
        ]);

        // 初期状態でいいね数が0であることを確認
        $this->assertEquals(0, $item->favoritesCount());

        // テスト手順1: ユーザーにログインする
        $this->actingAs($loginUser);

        // テスト手順2: 商品詳細ページを開く
        $response = $this->get("/item/{$item->id}");
        $response->assertStatus(200);

        // 初期状態では「いいねしていない」状態のアイコンが表示
        $response->assertSee('☆');
        $response->assertSee('<span class="favorite-count">0</span>', false);

        // テスト手順3: いいねアイコンを押下（APIエンドポイントにPOSTリクエスト）
        $likeResponse = $this->post("/item/{$item->id}/favorite");

        // 期待挙動: いいねした商品として登録され、いいね合計値が増加表示される
        $likeResponse->assertStatus(200);
        $likeResponse->assertJson([
            'success' => true,
            'is_favorited' => true,
            'favorites_count' => 1,
        ]);

        // データベースにいいねが保存されていることを確認
        $this->assertDatabaseHas('favorites', [
            'user_id' => $loginUser->id,
            'item_id' => $item->id,
        ]);

        // いいね数が増加していることを確認
        $this->assertEquals(1, $item->fresh()->favoritesCount());

    }

    /**
     * 再度いいねアイコンを押下することによって、いいねを解除することができる
     * 
     * テスト内容：再度いいねアイコンを押下することによって、いいねを解除することができる
     * 
     * @test
     */
    public function 再度いいねアイコンを押下することによっていいねを解除することができる()
    {
        // テスト準備：既にいいね済みの状態を作成
        $loginUser = User::factory()->withProfile()->create();
        
        $item = Item::factory()->create([
            'name' => 'いいね解除テスト商品',
        ]);

        // 既にいいね済みの状態にする
        Favorite::factory()->create([
            'user_id' => $loginUser->id,
            'item_id' => $item->id,
        ]);

        // いいね数が1であることを確認
        $this->assertEquals(1, $item->favoritesCount());

        // テスト手順1: ユーザーにログインする
        $this->actingAs($loginUser);

        // テスト手順2: 商品詳細ページを開く
        $response = $this->get("/item/{$item->id}");
        $response->assertStatus(200);

        // いいね済み状態であることを確認
        $response->assertSee('★');
        $response->assertSee('<span class="favorite-count">1</span>', false);

        // テスト手順3: いいねアイコンを押下（2回目のクリック = 解除）
        $unlikeResponse = $this->post("/item/{$item->id}/favorite");

        // 期待挙動: いいねが解除され、いいね合計値が減少表示される
        $unlikeResponse->assertStatus(200);
        $unlikeResponse->assertJson([
            'success' => true,
            'is_favorited' => false,
            'favorites_count' => 0,
        ]);

        // データベースからいいねが削除されていることを確認
        $this->assertDatabaseMissing('favorites', [
            'user_id' => $loginUser->id,
            'item_id' => $item->id,
        ]);

        // いいね数が減少していることを確認
        $this->assertEquals(0, $item->fresh()->favoritesCount());

    }
}
