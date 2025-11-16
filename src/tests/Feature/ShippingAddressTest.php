<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use Mockery;
use Stripe\StripeClient;

class ShippingAddressTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 送付先住所変更画面にて登録した住所が商品購入画面に正しく反映される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['is_sold' => false]);

        $this -> actingAs($user);

        // 「更新する」ボタンを押す（PATCH送信）
        $response = $this->patch(route('purchase.updateAddress', ['item_id' => $item->id]),[
            'shipping_postal_code'  => '000-0000',
            'shipping_address_line' => 'テスト市1-1',
            'shipping_building'     => 'テストビル',
        ]);

        $response->assertRedirect(route('purchase.create', ['item_id' => $item->id]));

        // 商品購入画面を再度開く
        $purchasePage = $this->withoutMiddleware()->get(route('purchase.create', ['item_id' => $item->id]));
        $purchasePage->assertSee('000-0000');
        $purchasePage->assertSee('テスト市1-1');
        $purchasePage->assertSee('テストビル');        $purchasePage->assertStatus(200);

    }

    /** @test */
    public function 購入した商品に送付先住所が紐づいて登録される()
    {
        // ユーザーと商品を作成
        $user = User::factory()->create();
        $item = Item::factory()->create(['is_sold' => false]);

        $this->actingAs($user);

        // Stripe モック作成
        $mockSession = (object)[
            'id' => 'sess_123',
            'payment_method_types' => ['card'],
            'metadata' => (object)['item_id' => $item->id],
        ];

        $mockSessions = Mockery::mock();
        $mockSessions->shouldReceive('retrieve')->andReturn($mockSession);

        $mockCheckout = Mockery::mock();
        $mockCheckout->sessions = $mockSessions;

        $mockStripe = Mockery::mock(StripeClient::class);
        $mockStripe->checkout = $mockCheckout;

        $this->app->instance(\App\Http\Controllers\PurchaseController::class,
        new \App\Http\Controllers\PurchaseController($mockStripe));

        // --- 送付先住所更新 ---
        $response = $this->withSession([
                'shipping_postal_code' => '123-4567',
                'shipping_address_line' => 'テスト市1-1',
                'shipping_building' => 'テストビル',
            ])->get(route('purchase.success', ['session_id' => $mockSession->id]));

        // --- DB 確認 ---
        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => 'card',
            'payment_status' => 'paid',
            'shipping_postal_code' => '123-4567',
            'shipping_address_line' => 'テスト市1-1',
            'shipping_building' => 'テストビル',
        ]);
        $response->assertRedirect(route('index'));
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
