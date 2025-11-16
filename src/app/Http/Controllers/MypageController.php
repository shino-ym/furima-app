<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ProfileRequest;

class MypageController extends Controller
{
    /**
     * マイページ表示
     * - タブ切替（購入 / 出品）
     * - 関連アイテムを取得
     */
    public function show(Request $request)
    {
        $tab = $request->get('tab', 'buy');
        $user = auth()->user();

        if ($tab === 'buy') {
            // 購入した商品のみ取得
            $items = $user->purchases()->with('item')->get()->map(function($purchase){
                return $purchase->item;
            });
        } else {
            // 出品した商品を取得
            $items = $user->items()->get();
        }

        return view('mypages.show', compact('user','tab','items'));
    }

    /**
     * プロフィール編集ページ表示
     */

    public function editProfile()
    {
        $user = auth()->user();

        return view('mypages.edit', compact('user'));
    }

    /**
     * プロフィール更新処理
     * - プロフィール画像のアップロード対応
     * - 古い画像の削除
     */
    public function updateProfile(ProfileRequest $request)
    {
        $user = auth()->user();

        $data = $request->validated();

        if ($request->hasFile('profile_image') && $request->file('profile_image')->isValid()) {
            // 古い画像があれば削除
            if ($user->profile_image) {
                Storage::delete($user->profile_image);
            }

            // 新しい画像を保存
            $path = $request->file('profile_image')->store('images/profile_images', 'public');
            $data['profile_image'] = $path;
        }

        // ユーザー情報を更新
        $user->update($data);

        return redirect()->route('index')->with('success', 'プロフィールを更新しました');
    }
}
