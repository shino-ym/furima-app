<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Like;



class MylistTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function いいねした商品だけがマイリストに表示される()
    {
        // テストユーザーを作成
        $user = User::factory()->create();
        // ユーザーがいいねする商品
        $likedItem = Item::factory()->create(['name' => 'いいね商品']);
        // いいねしていない商品
        $unlikedItem = Item::factory()->create(['name' => '未いいね商品']);

        // $userが$likedItemにいいねをした状態を作成
        Like::factory()->create([
            'user_id' => $user->id,
            'item_id' => $likedItem->id,
        ]);

        // ログイン状態にする
        $this->actingAs($user);
        // マイリストを表示
        $response = $this->get('/?tab=mylist');
        // マイリストにいいねをした商品が表示されているか確認
        $response->assertSeeText('いいね商品');
        // いいねしていない商品が表示されていないか確認
        $response->assertDontSeeText('未いいね商品');
    }

    /** @test */
    public function 購入済み商品に「Sold」のラベルが表示される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['is_sold'=>true]);


        $this->actingAs($user);

        Like::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->get('/?tab=mylist');

        $response->assertSee('Sold');
    }

    /** @test */
    public function 未認証の場合は表示されない()
    {
        $response = $this->get('/?tab=mylist');

        // 今のユーザーがログインしていないことを確認
        $this->assertGuest();
        // 何も表示されないことを確認
        $response->assertDontSee('Sold');
    }
}
