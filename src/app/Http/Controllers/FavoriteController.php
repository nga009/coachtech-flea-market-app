<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FavoriteController extends Controller
{
    
    // お気に入りクリック
    public function toggle(Item $item)
    {
        Log::info('Ajax request received--toggle start');

        $user = auth()->user();
        $item_id = $item->id;
        
        $favorite = $user->favorites()->where('item_id', $item_id)->first();

        if ($favorite) {
            // お気に入り解除
            $favorite->delete();
            $isFavorited = false;
        } else {
            // お気に入り追加
            $user->favorites()->create(['item_id' => $item_id]);
            $isFavorited = true;
        }

        return response()->json([
            'success' => true,
            'is_favorited' => $isFavorited,
            'favorites_count' => $item->favoritesCount(),
        ]);
    }

}
