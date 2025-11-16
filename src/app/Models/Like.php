<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'user_id',
    ];

    /**
     * 「いいね」をしたユーザー
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 「いいね」がついた商品
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
