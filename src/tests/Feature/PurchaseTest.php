<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Purchase;
use Mockery;
use Stripe\StripeClient;


class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Stripeモックを用いて「購入→成功」までの流れをテスト
     */
    /** @test */
    public function 「購入する」ボタンを押すと購入が完了する()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['is_sold' => false]);

        $this -> actingAs($user);

        // Stripe モックの作成
        $mockSession = (object)[
            'id' => 'cs_test_123',
            'payment_method_types' => ['card'],
            'metadata' => (object)['item_id' => $item->id],
            'url' => '/dummy-checkout-url',
        ];

        $mockSessions = Mockery::mock();
        $mockSessions->shouldReceive('create')->andReturn($mockSession);
        $mockSessions->shouldReceive('retrieve')->andReturn($mockSession);

        $mockCheckout = Mockery::mock();
        $mockCheckout->sessions = $mockSessions;

        $mockStripe = Mockery::mock(StripeClient::class);
        $mockStripe->checkout = $mockCheckout;

        // PurchaseController にモックを注入
        $this->app->instance(\App\Http\Controllers\PurchaseController::class, new \App\Http\Controllers\PurchaseController($mockStripe));

        // 購入画面を開く
        $response = $this->get(route('purchase.create', ['item_id' => $item->id]));
        $response->assertStatus(200);
        $response->assertSee('購入する');

        // チェックアウト処理
        $response = $this->post(route('purchase.checkout'), [
            'item_id' => $item->id,
            'payment_method' => 'card',
        ]);

        $response->assertRedirect('/dummy-checkout-url');

        // 成功処理
        $response = $this->get(route('purchase.success', ['session_id' => $mockSession->id]));

        // DB登録確認
        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => 'card',
            'payment_status' => 'paid',
        ]);

        // 商品が販売済み状態に更新されているか
        $this->assertTrue((bool) $item->fresh()->is_sold);

        // 成功後のリダイレクト確認
        $response->assertRedirect(route('index'));
    }

    /** @test */
    public function 購入した商品は商品一覧画面にて「Sold」と表示される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['is_sold' => false]);

        $this -> actingAs($user);

        // Stripe モックの作成
        $mockSession = (object)[
            'id' => 'cs_test_123',
            'payment_method_types' => ['card'],
            'metadata' => (object)['item_id' => $item->id],
            'url' => '/dummy-checkout-url',
        ];

        $mockSessions = Mockery::mock();
        $mockSessions->shouldReceive('create')->andReturn($mockSession);
        $mockSessions->shouldReceive('retrieve')->andReturn($mockSession);

        $mockCheckout = Mockery::mock();
        $mockCheckout->sessions = $mockSessions;

        $mockStripe = Mockery::mock(StripeClient::class);
        $mockStripe->checkout = $mockCheckout;

        // PurchaseController にモックを注入
        $this->app->instance(\App\Http\Controllers\PurchaseController::class, new \App\Http\Controllers\PurchaseController($mockStripe));

        // 購入画面
        $response = $this->get(route('purchase.create', ['item_id' => $item->id]));
        $response->assertStatus(200);
        $response->assertSee('購入する');

        // チェックアウト処理
        $response = $this->post(route('purchase.checkout'), [
            'item_id' => $item->id,
            'payment_method' => 'card',
        ]);

        $response->assertRedirect('/dummy-checkout-url');

        // 成功処理
        $response = $this->get(route('purchase.success', ['session_id' => $mockSession->id]));

        // DB登録確認
        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => 'card',
            'payment_status' => 'paid',
        ]);

        // 商品が販売済み状態に更新されているか
        $this->assertTrue((bool) $item->fresh()->is_sold);

        // リダイレクトした商品一覧で購入した商品にSoldの文字があるか確認
        $listResponse = $this->get(route('index'));
        $listResponse->assertSee('Sold');
    }

    /** @test */
    public function 購入した商品がプロフィールの購入した商品一覧に追加されている()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['is_sold' => false]);

        $this -> actingAs($user);

        // --- Stripe モックの作成 ---
        $mockSession = (object)[
            'id' => 'cs_test_123',
            'payment_method_types' => ['card'],
            'metadata' => (object)['item_id' => $item->id],
            'url' => '/dummy-checkout-url',
        ];

        $mockSessions = Mockery::mock();
        $mockSessions->shouldReceive('create')->andReturn($mockSession);
        $mockSessions->shouldReceive('retrieve')->andReturn($mockSession);

        $mockCheckout = Mockery::mock();
        $mockCheckout->sessions = $mockSessions;

        $mockStripe = Mockery::mock(StripeClient::class);
        $mockStripe->checkout = $mockCheckout;

        // PurchaseController にモックを注入
        $this->app->instance(\App\Http\Controllers\PurchaseController::class, new \App\Http\Controllers\PurchaseController($mockStripe));

        // --- 購入画面 ---
        $response = $this->get(route('purchase.create', ['item_id' => $item->id]));
        $response->assertStatus(200);
        $response->assertSee('購入する');

        // --- チェックアウト処理 ---
        $response = $this->post(route('purchase.checkout'), [
            'item_id' => $item->id,
            'payment_method' => 'card',
        ]);

        // 購入完了状態を再現
        $item->update(['is_sold' => true]);
        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => 'card',
            'payment_status' => 'paid',
            'amount' => $item->price,
            'shipping_postal_code' => '000-0000',
            'shipping_address_line' => 'テスト市1-1',
            'shipping_building' => 'テストビル',
        ]);

        // --- DB確認 ---
        $this->assertDatabaseHas('items', ['id' => $item->id, 'is_sold' => true]);
        $this->assertDatabaseHas('purchases', ['user_id' => $user->id, 'item_id' => $item->id]);

        // --- マイページ確認 ---
        $mypageResponse = $this->get(route('mypage.index', ['tab' => 'buy']));
        $mypageResponse->assertStatus(200)
                    ->assertSee('購入した商品')
                    ->assertSee($item->name);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
