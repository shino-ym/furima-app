<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'brand_name',
        'item_image',
        'price',
        'description',
        'is_sold',
        'condition',
    ];

    /**
     * 商品の状態（コンディション）一覧
     */
    public static function conditions(): array
    {
        return [
            '良好',
            '目立った傷や汚れなし',
            'やや傷や汚れあり',
            '状態が悪い',
        ];
    }

    /**
     * この商品を出品したユーザー（出品者）
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 出品者を明示的に取得（user() と同じ）
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 商品に紐づくカテゴリー（多対多）
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class,'category_item', 'item_id', 'category_id');
    }

    /**
     * 商品に付いたコメント（新しい順で取得）
     */
    public function comments()
    {
        return $this->hasMany(Comment::class)->orderBy('created_at', 'desc');
    }

    /**
     * 商品に「いいね」したユーザー（多対多）
     */
    public function likes()
    {
        return $this->belongsToMany(User::class, 'likes','item_id', 'user_id')->withPivot('created_at');
    }

    /**
     * 商品購入情報（1対1）
     */
    public function purchase()
    {
        return $this->hasOne(Purchase::class);
    }
}


