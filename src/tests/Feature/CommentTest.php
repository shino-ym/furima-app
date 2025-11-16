<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Comment;


class CommentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function コメントが保存され、コメント数が増加する()
    {

        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this -> actingAs($user);

        // コメント内容を準備
        $commentData = [
            'comment' => 'とても良い商品です！',
        ];

        // コメント処理を実行
        $response = $this->post(route('comments.store', ['id' => $item->id]),$commentData);

        // 成功を確認
        $response->assertStatus(200);

        // DBに登録されているか確認
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'comment' => 'とても良い商品です！',
        ]);

        // コメント数が1増えているか確認
        $this->assertEquals(1, $item->comments()->count());

         // JSONレスポンスを確認（Ajax対応用）
        $response->assertJson([
            'status' => 'success',
            'comment_count' => 1,
        ]);
    }

        /** @test */
    public function ログイン前のユーザーはコメントを送信できない()
    {
        $item = Item::factory()->create();

        $commentData = [
            'comment' => 'とても良い商品です！',
        ];

        $response = $this->post(route('comments.store', ['id' => $item->id]),$commentData);

        // ログインページにリダイレクトされることを確認
        $response->assertRedirect('/login');

        // コメントが保存されていないことを確認
        $this->assertDatabaseMissing('comments', [
            'comment' => 'とても良い商品です！',
        ]);

        $this->assertGuest();
    }

    /** @test */
    public function コメントが入力されていない場合、バリデーションメッセージが表示される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this -> actingAs($user);

        // コメント内容を準備
        $commentData = [
            'comment' => '',
        ];

        // コメント送信を試みる（POSTリクエスト）
        $response = $this->post(route('comments.store', ['id' => $item->id]), $commentData);

        // commentに対してバリデーションエラーがあることを確認
        $response->assertSessionHasErrors(['comment']);

        // エラーメッセージが「コメントを入力してください」であることを確認
        $this->assertEquals(
            'コメントを入力してください',
            session('errors')->first('comment')
        );
    }

    /** @test */
    public function コメントが255字以上の場合、バリデーションメッセージが表示される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this -> actingAs($user);

        // --- 256文字のコメントを作成 ---
        $longComment = str_repeat('あ', 256);

        // --- コメント内容を準備 ---
        $commentData = [
            'comment' => $longComment,
        ];

        // コメント送信を試みる（POSTリクエスト）
        $response = $this->post(route('comments.store', ['id' => $item->id]), $commentData);

        // commentに対してバリデーションエラーがあることを確認
        $response->assertSessionHasErrors(['comment']);

        // エラーメッセージが「コメントは255文字以内で入力してください」であることを確認
        $this->assertEquals(
            'コメントは255文字以内で入力してください',
            session('errors')->first('comment')
        );
    }
}
