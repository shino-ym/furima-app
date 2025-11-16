<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ExhibitionRequest;
use App\Models\Category;
use App\Models\Item;



class SellController extends Controller
{
    /**
     * 出品ページ表示
     * - カテゴリー一覧を取得
     * - 商品状態（コンディション）一覧を準備
     */
    public function create(Request $request)
    {
        $categories = Category::all();

        $conditions = [
            '良好',
            '目立った傷や汚れなし',
            'やや傷や汚れあり',
            '状態が悪い',
        ];

        return view('sell.create', compact('categories','conditions'));
    }

    /**
     * 出品商品保存
     * - バリデーション済みデータを取得
     * - 画像をストレージに保存
     * - Itemモデルに情報を保存
     * - カテゴリーとの中間テーブルを更新
     */
    public function store(ExhibitionRequest $request)
    {
        $validated = $request->validated();

         // 画像を保存
        $imagePath = $request->file('item_image')->store('images', 'public');

        // 商品情報保存
        $item = new Item();
        $item->user_id = auth()->id();
        $item->item_image = $imagePath;
        $item->name = $validated['name'];
        $item->brand_name = $validated['brand_name'] ?? null;
        $item->description = $validated['description'];
        $item->price = $validated['price'];
        $item->condition = $validated['condition'];
        $item->save();

        // 中間テーブルにカテゴリーを紐付け
        if (!empty($validated['category_id'])) {
            $item->categories()->attach($validated['category_id']);
        }

        return redirect()->route('mypage.index', ['tab' => 'sell'])
            ->with('success', '商品が登録されました。');
    }
}
