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
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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

    /* リレーション */
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }
    
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoriteItems()
    {
        return $this->belongsToMany(Item::class, 'favorites');
    }

    public function sellingItems()
    {
        return $this->hasMany(Item::class, 'seller_id');
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'buyer_id');
    }

    // ユーザーが購入した商品のリレーション（中間テーブル経由）
    public function purchasedItems()
    {
        return $this->belongsToMany(Item::class, 'purchases', 'buyer_id', 'item_id');
    }


    /* お気に入り商品を商品名で絞り込むローカルスコープ */
    public function scopeFavoriteItemsWithSearch($query, $keyword = null)
    {
        return $this->favoriteItems()
            ->when($keyword, function ($query, $keyword) {
                return $query->where('items.name', 'like', '%' . $keyword . '%');
            })
            ->orderBy('favorites.created_at', 'desc');
    }
}
