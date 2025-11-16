<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Like;


class SearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 部分一致する商品が表示される()
    {
        // 準備：3つの商品を作成
        $itemA = Item::factory()->create(['name' => 'りんごジュース']);
        $itemB = Item::factory()->create(['name' => 'オレンジジュース']);
        $itemC = Item::factory()->create(['name' => 'スマホケース']);

        // 検索キーワード
        $keyword = 'ジュース';

        // 実行：検索リクエストを送信
        $response = $this->get("/?tab=default&keyword={$keyword}");

        $response->assertStatus(200);

        // 検証：部分一致するものだけが表示される
        $response->assertSee($itemA->name);
        $response->assertSee($itemB->name);
        $response->assertDontSee($itemC->name);

        $response->assertStatus(200);
    }

    /** @test */
    public function 検索状態がマイリストでも保持されている()
    {
        // 準備
        $user = User::factory()->create();
        $item1 = Item::factory()->create(['name' => 'チョコレートケーキ']);
        $item2 = Item::factory()->create(['name' => 'チョコミントアイス']);
        $item3 = Item::factory()->create(['name' => 'いちごショート']);

        // ユーザーがいいねした商品（マイリストに出るのはこれだけ）
        Like::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item1->id,
        ]);
        Like::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item2->id,
        ]);

        // ログイン状態にする
        $this->actingAs($user);

        // 検索実行（keyword=チョコ）
        $response = $this->get('/?keyword=チョコ');

        $response->assertStatus(200);

        // 検索結果が表示される（ホーム画面）
        $response->assertSee('チョコレートケーキ');
        $response->assertSee('チョコミントアイス');
        $response->assertDontSee('いちごショート');

        // マイリストに遷移してもキーワードが保持されている
        $response = $this->get('/?tab=default&keyword=チョコ');

        $response->assertSee('チョコレートケーキ');
        $response->assertSee('チョコミントアイス');
        $response->assertDontSee('いちごショート');

        // 検索キーワードがフォーム内に保持されていることも確認
        $response->assertSee('value="チョコ"', false);
    }
}
