<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'item_id',
        'payment_method',
        'payment_status',
        'stripe_payment_id',
        'amount',
        'shipping_postal_code',
        'shipping_address_line',
        'shipping_building',
    ];

    /**
     * 購入者
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 購入された商品
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
