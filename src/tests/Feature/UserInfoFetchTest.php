<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;


class UserInfoFetchTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function ユーザーの必要な情報が正しく表示される()
    {
        $user = User::factory()->create();

        // 出品商品
        $item = Item::factory()->create(['user_id' => $user->id]);

        // 購入商品
        $purchaseItem = Item::factory()->create();
        Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $purchaseItem->id,
        ]);

        // ログイン
        $this->actingAs($user);

        // プロフィール編集ページ確認
        $response = $this->get(route('mypage.profile.edit'));
        $response->assertStatus(200)
            ->assertSee($user->name)
            ->assertSee('default-profile.png');

        // 購入商品タブ
        $response = $this->get('/mypage?tab=buy');
        $response->assertStatus(200)
                ->assertSee($purchaseItem->name);

        // 出品商品タブ
        $response = $this->get('/mypage?tab=sell');
        $response->assertStatus(200)
                ->assertSee($item->name);
    }
}