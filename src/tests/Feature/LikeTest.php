<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Like;


class LikeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function いいねした商品として登録され、いいね合計値が増加表示される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this -> actingAs($user);

         // いいね処理を実行
        $response = $this->post(route('items.like', ['id' => $item->id]));

        // 成功を確認
        $response->assertStatus(200);

        // DBに登録されているか確認
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // いいね数が1増えているか確認
        $this->assertEquals(1, $item->likes()->count());
    }

    /** @test */
    public function いいねアイコンが押下された状態では色が変化する()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this -> actingAs($user);

         //  いいね処理を実行
        $response = $this->post(route('items.like', ['id' => $item->id]));

        //  成功を確認
        $response->assertStatus(200);

        // likesテーブルに登録されているか（＝いいね済み状態）
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response->assertJson(['liked' => true]);

    }

    /** @test */
    public function 再度いいねアイコンを押下すると、いいねが解除され、いいね合計数が減少表示される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this -> actingAs($user);

         // いいね処理を実行（例：POST /items/{id}/like）
        $response = $this->post(route('items.like', ['id' => $item->id]));

        // 成功を確認
        $response->assertStatus(200);

        // DBに登録されているか確認
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // いいね数が1増えているか確認
        $this->assertEquals(1, $item->likes()->count());

        // 2回目：もう一度同じルートを叩く（いいね解除
        $response = $this->post(route('items.like', ['id' => $item->id]));
        $response->assertStatus(200);

        // likesテーブルから削除されていることを確認
        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // いいね数が0に戻っていることを確認
        $this->assertEquals(0, $item->likes()->count());
    }
}
