<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Like;
use App\Models\Category;
use App\Models\Comment;


class ItemShowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 全ての情報が商品詳細ページに表示される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $user->id,
            'condition' =>'良好' ,
            'name' => 'テスト商品',
            'price' => 1500,
            'brand_name' => 'テストブランド',
            'description' => 'これはテスト商品の説明です。',
            'item_image' => 'test.png',
        ]);
        // カテゴリー
        $categories = Category::factory()->create();
        $item->categories()->attach($categories->pluck('id'));
        // この商品に対していいねが３件
        Like::factory()->count(3)->create(['item_id' => $item->id]);
        // コメントが2件
        Comment::factory()->count(2)->create(['item_id' => $item->id]);

        // 商品詳細ページを開く
        $response = $this->get("/items/{$item->id}");
        // ページがちゃんと表示されたか確認
        $response->assertStatus(200);
        $response->assertSee('良好');
        $response->assertSee('テスト商品');
        $response->assertSee('¥1,500');
        $response->assertSee('テストブランド');
        $response->assertSee('いいね'); // いいね数表示
        $response->assertSee('コメント(');
        $response->assertSee('2');
        $response->assertSee('これはテスト商品の説明です。');
        $response->assertSee($categories->name);
    }

    /** @test */
    public function 複数選択されたカテゴリーが商品詳細ページに表示されている()
    {
        // テストユーザーを作る
        $user = User::factory()->create();
        // このユーザーが出品した商品を一つ作成
        $item = Item::factory()->create([
            'user_id' => $user->id,
            'condition' =>'良好' ,
            'name' => 'テスト商品',
            'price' => 1500,
            'brand_name' => 'テストブランド',
            'description' => 'これはテスト商品の説明です。',
            'item_image' => 'test.png',
        ]);

        // 商品に２つのカテゴリーを紐づける
        $categories = Category::factory()->count(2)->create();
        $item->categories()->attach($categories->pluck('id'));

        $response = $this->get("/items/{$item->id}");

        $response->assertStatus(200);
        $response->assertSee('良好');
        $response->assertSee('テスト商品');
        $response->assertSee('¥1,500');
        $response->assertSee('テストブランド');
        $response->assertSee('いいね'); // いいね数表示
        $response->assertSee('コメント(');
        $response->assertSee('2');
        $response->assertSee('これはテスト商品の説明です。');
        $response->assertSee($categories[0]->name);
        // 一つ目のカテゴリー
        $response->assertSee($categories[1]->name);
        // 二つ目のカテゴリー
    }
}
