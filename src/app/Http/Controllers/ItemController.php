<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    // 商品一覧画面表示
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'all');
        $keyword = $request->get('keyword', '');
        $user = auth()->user();
        $items = [];

        // ログインしている場合
        if ($user!==null) {
            // メール認証・プロフィール登録チェック
            $this->middleware('verified');
            if (!auth()->user()->profile) {
               return redirect()->route('profile.form');
            }

            // 検索
            // マイリストタブの場合
            if ($tab === 'mylist') {
                $items = $user->favoriteItemsWithSearch($keyword)->get();

            // おすすめタブの場合
            } else {
                $items = Item::forItemList($keyword, true)->get();                
            }

        // ログインしてない場合
        } else {
            if ($tab === 'all') {
                $items = Item::forItemList($keyword, false)->get();             
            }
        }

        return view('index', compact('items', 'tab', 'keyword'));

    }

    // 商品詳細画面表示
    public function show($item_id)
    {
        $item = Item::find($item_id);        

        return view('show', compact('item'));
    }
}
