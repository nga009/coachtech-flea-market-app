<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Comment;


class CommentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ログイン済みのユーザーはコメントを送信できる
     * 
     * テスト内容：ログイン済みのユーザーはコメントを送信できる
     * 
     * @test
     */
    public function ログイン済みのユーザーはコメントを送信できる()
    {
        // テスト準備
        $loginUser = User::factory()
            ->withProfileData(['name' => 'コメントユーザー'])
            ->create();
                
        $item = Item::factory()->create([
            'name' => 'コメントテスト商品',
        ]);

        // 初期状態でコメント数が0であることを確認
        $this->assertEquals(0, $item->commentsCount());

        // テスト手順1: ユーザーにログインする
        $this->actingAs($loginUser);

        // 商品詳細ページを表示してコメントフォームが表示されることを確認
        $detailResponse = $this->get("/item/{$item->id}");
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('コメントを送信する');

        // テスト手順2: コメントを入力する
        // テスト手順3: コメントボタンを押す
        $commentData = [
            'comment' => 'これは素晴らしい商品ですね！購入を検討しています。'
        ];

        $response = $this->post("/item/{$item->id}/comments", $commentData);

        // 期待挙動: コメントが保存され、コメント数が増加する
        // データベースにコメントが保存されていることを確認
        $this->assertDatabaseHas('comments', [
            'item_id' => $item->id,
            'user_id' => $loginUser->id,
            'comment' => 'これは素晴らしい商品ですね！購入を検討しています。'
        ]);

        // コメント数が増加していることを確認
        $this->assertEquals(1, $item->fresh()->commentsCount());

        // 追加したコメントが表示されていることを確認
        $updatedResponse = $this->get("/item/{$item->id}");
        $updatedResponse->assertStatus(200);
        $updatedResponse->assertSee('これは素晴らしい商品ですね！購入を検討しています。');
        $updatedResponse->assertSee('span class="comment-count">1</span>', false);
    }

    /**
     * ログイン前のユーザーはコメントを送信できない
     * 
     * テスト内容：ログイン前のユーザーはコメントを送信できない
     * 
     * @test
     */
    public function ログイン前のユーザーはコメントを送信できない()
    {
        // テスト準備
        $item = Item::factory()->create([
            'name' => 'コメントテスト商品',
        ]);

        // 商品詳細ページを表示してコメントフォームが表示されることを確認
        $detailResponse = $this->get("/item/{$item->id}");
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('<button type="submit" class="comment-submit" disabled>', false);

    }

    /**
     * コメントが入力されていない場合、バリデーションメッセージが表示される
     * 
     * テスト内容：コメントが入力されていない場合、バリデーションメッセージが表示される
     * 
     * @test
     */
    public function コメントが入力されていない場合、バリデーションメッセージが表示される()
    {
        // テスト準備
        $loginUser = User::factory()
            ->withProfileData(['name' => 'コメントユーザー'])
            ->create();
                
        $item = Item::factory()->create([
            'name' => 'コメントテスト商品',
        ]);

        // 初期状態でコメント数が0であることを確認
        $this->assertEquals(0, $item->commentsCount());

        // テスト手順1: ユーザーにログインする
        $this->actingAs($loginUser);

        // 商品詳細ページを表示してコメントフォームが表示されることを確認
        $detailResponse = $this->get("/item/{$item->id}");
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('コメントを送信する');

        // テスト手順2: コメントボタンを押す
        $commentData = [
            'comment' => ''
        ];

        $response = $this->post("/item/{$item->id}/comments", $commentData);

        // 期待挙動: バリデーションメッセージが表示される
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['comment']);
        $response->assertSessionHasErrors([
            'comment' => 'コメントを入力してください'
        ]);

        // データベースにコメントが保存されていないことを確認
        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'user_id' => $loginUser->id,
        ]);

        // コメント数が0のまま
        $this->assertEquals(0, $item->commentsCount());

        // リダイレクト後のページでエラーメッセージが表示されることを確認
        $this->followRedirects($response)
             ->assertSee('コメントを入力してください');

    }

    /**
     * コメントが255字以上の場合、バリデーションメッセージが表示される
     * 
     * テスト内容：コメントが255字以上の場合、バリデーションメッセージが表示される
     * 
     * @test
     */
    public function コメントが255字以上の場合、バリデーションメッセージが表示される()
    {
        // テスト準備
        $loginUser = User::factory()
            ->withProfileData(['name' => 'コメントユーザー'])
            ->create();
                
        $item = Item::factory()->create([
            'name' => 'コメントテスト商品',
        ]);

        // 初期状態でコメント数が0であることを確認
        $this->assertEquals(0, $item->commentsCount());

        // テスト手順1: ユーザーにログインする
        $this->actingAs($loginUser);

        // 商品詳細ページを表示してコメントフォームが表示されることを確認
        $detailResponse = $this->get("/item/{$item->id}");
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('コメントを送信する');

        // テスト手順2: 255文字以上入力する
        $longComment = str_repeat('あ', 256); // 256文字の文字列
        $commentData = [
            'comment' => $longComment
        ];

        // テスト手順3: コメントボタンを押す
        $response = $this->post("/item/{$item->id}/comments", $commentData);

        // 期待挙動: バリデーションメッセージが表示される
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['comment']);
        $response->assertSessionHasErrors([
            'comment' => 'コメントは255文字以内で入力してください'
        ]);

        // データベースにコメントが保存されていないことを確認
        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'user_id' => $loginUser->id,
        ]);

        // コメント数が0のまま
        $this->assertEquals(0, $item->commentsCount());

        // リダイレクト後のページでエラーメッセージが表示されることを確認
        $this->followRedirects($response)
             ->assertSee('コメントは255文字以内で入力してください');

    }

}
