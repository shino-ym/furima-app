<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;


class ItemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 全商品が表示される()
    {
        // 商品を複数作る
        $items = Item::factory()->count(3)->create();

        // 商品一覧ページを開く
        $response = $this->get('/');

        // レスポンスが正常
        $response->assertStatus(200);

        // 作った商品の名前が表示されていることを確認
        foreach ($items as $item) {
            $response->assertSee($item->name);
        }
    }

    /** @test */
    public function 購入済み商品に「Sold」のラベルが表示される()
    {
        // 売れた商品を作る
        $item = Item::factory()->create(['is_sold'=>true]);
        // 商品一覧ページを開く
        $response = $this->get('/');
        // ページ内にSoldという文字があるか確認
        $response->assertSee('Sold');
    }

        /** @test */
    public function 自分が出品した商品が一覧に表示されない()
    {
        // 自分のユーザーと出品した商品を作る
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'user_id'=>$user->id,
            'name'=>'自分の商品',
        ]);

        // 他人が出品した商品を作る
        $otherItem = Item::factory()->create([
            'name'=>'他人の商品',
        ]);

        // 自分としてログインして、商品ページを表示
        $this->actingAs($user);
        $response = $this->get('/');

        // 商品一覧に自分が出品したアイテムがないことをチェック
        $response->assertDontSee('自分の商品');
        // 商品一覧に他人が出した商品がちゃんと表示されているかチェック
        $response->assertSee('他人の商品');
    }
}


