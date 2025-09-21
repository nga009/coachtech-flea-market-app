<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'buyer_id',
        'shipping_postcode',
        'shipping_address',
        'shipping_building',
        'payment_method',
        'stripe_session_id',
        'status',
    ];

    /* リレーション */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

}
