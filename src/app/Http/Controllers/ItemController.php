<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    /**
     * 商品一覧ページ
     * - タブ切替（おすすめ / マイリスト）
     * - キーワード検索
     * - ログインユーザー除外
     */
    public function index(Request $request)
    {
        $tab = $request->tab ?? 'default';
        $query = Item::query();

        $keyword = $request->keyword;

        // キーワード検索
        if ($keyword) {
            $query->where(function ($queryBuilder) use($keyword){
                $queryBuilder->where('name', 'like', "%{$keyword}%");
            });
        }

         // マイリストタブの場合の絞り込み
        if ($tab === 'mylist') {
            if (Auth::check()) {
                $query->whereHas('likes', function ($queryBuilder) {
                    $queryBuilder->where('user_id', Auth::id());
                });
            } else {
                // ログインしていない場合はマイリストを空にする
                $query->whereRaw('0 = 1');
            }
        }

         // ログインユーザー自身の出品は除外
        if (Auth::check()) {
            $query->where('user_id', '!=', Auth::id());
        }
        $items = $query->get();

        return view('items.index', compact('tab', 'items', 'request'));
    }


    /**
     * 商品詳細ページ
     * - 関連カテゴリ、コメント、いいね情報をロード
     * - ログインユーザーのいいね状態判定
     * - 購入済み判定
     */

    public function show($id)
    {
        $item = Item::with([
            'categories',
            'comments.user',
            'likes',
            'purchase'
        ])->findOrFail($id);

        $liked = auth()->check() ? $item->likes->contains('id', auth()->id()) : false;

        $isSold = $item->is_sold;

        return view('items.show', compact('item', 'liked', 'isSold'));
    }
}