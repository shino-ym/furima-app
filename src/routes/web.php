<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\Auth\CustomRegisterController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SellController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// ===============================
// 🔸 トップページ・商品関連
// ===============================
Route::get('/', [ItemController::class, 'index'])->name('index');
Route::get('/items/{id}', [ItemController::class, 'show'])->name('items.show');

// ===============================
// 🔸 会員登録。メール認証
// ===============================

Route::post('/register', [CustomRegisterController::class, 'store'])->name('register');

// メール認証誘導画面
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->name('verification.notice');

// メールリンククリック時
Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
if (! URL::hasValidSignature($request)) {
        abort(403, '無効な署名です');
    }
    $user = User::findOrFail($id);

    if (! $user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
    }

    Auth::login($user);
    $request->session()->regenerate();

    return redirect()->route('mypage.profile.edit')->with('success', 'メール認証が完了しました！');
})->name('verification.verify')->middleware('signed');

// 確認メール再送信
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', '確認メールを送信しました');
})->middleware('throttle:6,1')->name('verification.send');

// ===============================
// 🔸 Stripe決済結果
// ===============================
// ※ Stripeがリダイレクトするため、認証外でもアクセス可能にしている
Route::get('/purchase/success', [PurchaseController::class, 'success'])->name('purchase.success');
Route::get('/purchase/cancel', [PurchaseController::class, 'cancel'])->name('purchase.cancel');

// ===============================
// 🔸 ログイン後のユーザー専用ページ
// ===============================
Route::middleware(['auth'])->group(function () {
    // マイページ
    Route::get('/mypage', [MypageController::class, 'show'])->name('mypage.index');
    Route::get('/mypage/profile', [MypageController::class, 'editProfile'])->name('mypage.profile.edit');
    Route::patch('/mypage/profile', [MypageController::class, 'updateProfile'])->name('mypage.profile.update');

    // 購入関連
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'create'])->name('purchase.create');
    Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'address'])->name('purchase.address');
    Route::patch('/purchase/address/{item_id}', [PurchaseController::class, 'updateAddress'])->name('purchase.updateAddress');
    Route::post('/purchase/checkout', [PurchaseController::class, 'checkout'])->name('purchase.checkout');

    // 出品関連
    Route::get('/sell', [SellController::class, 'create'])->name('sell.create');
    Route::post('/sell', [SellController::class, 'store'])->name('sell.store');

    // コメント投稿
    Route::post('/items/{id}/comments', [CommentController::class, 'store'])->name('comments.store');

    // いいね機能
    Route::post('/items/{id}/like', [LikeController::class, 'toggle'])->name('items.like');
});
