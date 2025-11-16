<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddressRequest;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use Stripe\StripeClient;

class PurchaseController extends Controller
{

    protected $stripe;

    public function __construct($stripe = null)
    {
        $secret = config('services.stripe.secret');
        if (!$secret && !$stripe) {
            throw new \RuntimeException('Stripe Secret is null! Check .env and config/services.php');
        }
        $this->stripe = $stripe ?? new StripeClient($secret);
    }

    // 商品購入画面表示
    public function create($item_id)
    {
        $user = auth()->user();
        $item = Item::findOrFail($item_id);
        $is_sold = $item->is_sold;

        // Bladeで使う住所はセッション優先、なければDBのデフォルト
        $postal_code   = session('shipping_postal_code', $user->default_postal_code);
        $address_line  = session('shipping_address_line', $user->default_address_line);
        $building      = session('shipping_building', $user->default_building);

        return view('purchases.create', compact(
            'user', 'item', 'item_id', 'postal_code', 'address_line', 'building','is_sold',
        ));
    }


    // 住所変更画面表示
    public function address($item_id)
    {
        $user = auth()->user();

        // セッション優先でフォームに値を渡す
        $postal_code  = session('shipping_postal_code');
        $address_line = session('shipping_address_line');
        $building     = session('shipping_building', '');

        return view('purchases.address', compact('user', 'item_id', 'postal_code', 'address_line', 'building'));
    }

    // 住所変更（セッションに保存）
    public function updateAddress(AddressRequest $request, $item_id)
    {
        $validated = $request->validated();

        session([
            'shipping_postal_code'  => $validated['shipping_postal_code'],
            'shipping_address_line' => $validated['shipping_address_line'],
            'shipping_building'     => $validated['shipping_building'] ?? '',
        ]);

        return redirect()->route('purchase.create', ['item_id' => $item_id])
                        ->with('success', '配送先住所を変更しました');
    }

    public function checkout(Request $request)
    {
        $itemId = $request->input('item_id');
        $item = Item::findOrFail($itemId);

        $paymentMethod = $request->input('payment_method'); // 'card' or 'konbini'

        // 支払いモードの設定
        $paymentMethodTypes = $paymentMethod === 'konbini'
            ? ['konbini'] : ['card'];

        // 商品情報
        $lineItems = [[
            'price_data' => [
                'currency' => 'jpy',
                'product_data' => [
                    'name' => $item->name,
                ],
                'unit_amount' => $item->price,
            ],
            'quantity' => 1,
        ]];

        // StripeのCheckoutセッションを作成
        $session = $this->stripe->checkout->sessions->create([
            'payment_method_types' => $paymentMethodTypes,
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('purchase.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('purchase.cancel'),
            'metadata' => [
            'item_id' => $request->input('item_id'),
            ],
        ]);

        // Stripeの決済ページにリダイレクト
        return redirect($session->url);
    }

    // 購入成功時処理
    public function success(Request $request)
    {
        $stripe = $this->stripe;

        // Stripeのセッション情報取得（決済確認）
        $sessionId = $request->query('session_id');
        if (!$sessionId) {
        return redirect()->route('index')->with('error', 'セッションIDがありません');
        }

        // StripeのCheckoutセッションを取得
        $session = $stripe->checkout->sessions->retrieve($sessionId);

        $itemId = $session->metadata->item_id;
        $user = auth()->user();
        $item = Item::findOrFail($itemId);

        // 二重登録防止
        if ($item->is_sold) {
            return redirect()->route('index')->with('error', 'この商品はすでに購入されています');
        }

        // セッションまたはデフォルト住所を取得
        $shipping_postal_code  = session('shipping_postal_code', $user->default_postal_code ?? '000-0000');
        $shipping_address_line = session('shipping_address_line', $user->default_address_line ?? 'テスト市1-1');
        $shipping_building     = session('shipping_building', $user->default_building ?? 'テストビル');

        // 購入情報保存
        Purchase::create([
            'user_id'               => $user->id,
            'item_id'               => $item->id,
            'payment_method'        => $session->payment_method_types[0], // 'card' or 'konbini'
            'payment_status'        => 'paid',
            'amount'                => $item->price,
            'shipping_postal_code'  => $shipping_postal_code,
            'shipping_address_line' => $shipping_address_line,
            'shipping_building'     => $shipping_building,
        ]);

        // 商品状態を更新
        $item->update(['is_sold' => true]);

        // セッションをクリア
        session()->forget(['shipping_postal_code', 'shipping_address_line', 'shipping_building']);

        return redirect()->route('index')->with('success', '購入が完了しました');
    }

    /**
     * 購入キャンセル画面
     */
    public function cancel()
    {
        return view('purchases.cancel');
    }
}

