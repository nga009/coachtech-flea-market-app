<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_image',
        'condition',
        'name',
        'brand',
        'description',
        'price',
        'seller_id',
        'is_sold',
    ];

    /* リレーション */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'item_categories');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoritedByUsers()
    {
        return $this->belongsToMany(User::class, 'favorites');
    }


    /* ヘルパーメソッド */
    // 指定ユーザーのお気に入りか判定
    public function isFavoritedBy(User $user = null)
    {
        if (!$user) {
            return false;
        }
        return $this->favorites()->where('user_id', $user->id)->exists();
    }

    // お気に入り数取得
    public function favoritesCount()
    {
        return $this->favorites()->count();
    }

    // コメント数取得
    public function commentsCount()
    {
        return $this->comments()->count();
    }

    // 金額フォーマット
    public function getFormattedPriceAttribute()
    {
        return '¥' . number_format($this->price);
    }

    /* ローカルスコープ */
    // 商品名で絞り込み
    public function scopeSearchByName($query, $keyword = null)
    {
        return $query->when($keyword, function ($query, $keyword) {
            return $query->where('name', 'like', '%' . $keyword . '%');
        });
    }    

    // 自分が出品者以外で絞り込み
    public function scopeExcludeCurrentUserItems($query)
    {
        return $query->when(auth()->check(), function ($query) {
            return $query->where('seller_id', '!=', auth()->id());
        });
    }

    // 登録日降順でソート
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
    
    // 商品一覧取得用の統合ローカルスコープ
    public function scopeForItemList($query, $keyword = null, $excludeCurrentUser = false)
    {
        return $query->searchByName($keyword)
            ->when($excludeCurrentUser, function ($query) {
                return $query->excludeCurrentUserItems();
            })
            ->latest();
    }    
}
