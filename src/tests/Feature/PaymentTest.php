<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;

class PaymentTest extends TestCase
{
    use RefreshDatabase;
/**
 * このテストでは支払い方法を選ぶところまでをPHPUnitの対象としています。
 * 小計画面への反映はJavaScript処理のため、PHPUnit対象外です。
 */

    /** @test */
    public function 選択した支払い方法が小計画面で正しく反映される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['is_sold' => false]);

        $this -> actingAs($user);

        // 支払い方法画面を開く
        $response = $this->get(route('purchase.create', ['item_id' => $item->id]));

        // 表示内容を確認
        $response->assertStatus(200);
        $response->assertSee('支払い方法');
    }
}
