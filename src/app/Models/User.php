<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'profile_image',
        'password',
        'default_postal_code',
        'default_address_line',
        'default_building',
    ];

    /**
     * ユーザーが出品した商品
     */
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    /**
     * ユーザーが書いたコメント
     */
    public function comments()
    {
        return $this->hasMany(Comment::class)->orderBy('created_at', 'desc');
    }

    /**
     * ユーザーがいいねした商品
     */
    public function likedItems()
    {
        return $this->belongsToMany(Item::class, 'likes')->withPivot('created_at');
    }

    /**
     * 送付先郵便番号（入力済み or デフォルト）
     */
    public function getShippingPostalCodeAttribute()
    {
        // 送付先が入力されていればそれ、なければプロフィール郵便番号
        return $this->default_shipping_postal_code ?? $this->default_postal_code;
    }

    /**
     * 送付先住所（入力済み or デフォルト）
     */
    public function getShippingAddressLineAttribute()
    {
        return $this->default_shipping_address_line ?? $this->default_address_line;
    }

    /**
     * 送付先建物名（入力済み or デフォルト）
     */
    public function getShippingBuildingAttribute()
    {
        return $this->default_shipping_building ?? $this->default_building;
    }

    /**
     * プロフィール画像URL
     */
    public function getProfileImageUrlAttribute()
    {
        if($this->profile_image){
            return asset('storage/profile_images/' .$this->profile_image);
        }else{
            return asset('images/profile_images/default-profile.png');
        }
    }

    /**
     * ユーザーの購入履歴
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    /**
     * パスワード等隠すカラム
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
