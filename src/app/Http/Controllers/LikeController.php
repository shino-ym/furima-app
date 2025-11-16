<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function __construct()
    {
        // 未ログインならログインページへリダイレクト
        $this->middleware('auth');
    }

    public function toggle($id)
    {
        $item = Item::findOrFail($id);
        $user = Auth::user();

        // すでにいいねしてるか確認
        $liked = $item->likes()->where('user_id', $user->id)->exists();

        if ($liked) {
            $item->likes()->detach($user->id);// いいね解除
            $liked_status = false;
        } else {
            $item->likes()->attach($user->id);// いいね追加
            $liked_status = true;
        }

        // 結果をJSONで返す
        return response()->json([
            'success' => true,
            'liked' => $liked_status,
            'count' => $item->likes()->count()
        ]);
    }
}
