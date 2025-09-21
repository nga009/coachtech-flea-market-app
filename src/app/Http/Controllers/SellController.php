<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Http\Requests\ExhibitionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SellController extends Controller
{
    // 出品画面　表示
    public function create(Request $request)
    {
        // カテゴリーを全件取得
        $categories = Category::all();

        return view('sell.create', compact('categories'));
    }

    // 出品登録
    public function store(ExhibitionRequest $request)
    {
        try {
            DB::beginTransaction();

            // 商品登録
            $itemData = [
                'condition' => $request->condition,
                'name' => $request->name,
                'brand' => $request->brand,
                'description' => $request->description,
                'price' => $request->price,
                'seller_id' => auth()->user()->id,
            ];

            $imagePath = $request->file('item_image')->store('item_images', 'public');
            $itemData['item_image'] = $imagePath;

            $item = Item::create($itemData);

            // 商品のカテゴリー登録
            $categories = $request->input('categories', []);
            foreach ($categories as $category) {
                ItemCategory::create([
                    'item_id' => $item->id,
                    'category_id' => $category,
                ]);            
            }

            // コミット
            DB::commit();

            return redirect()->route('items.index')->with('success', '出品ありがとうございました');

        } catch (\Exception $e) {
            // 例外が発生した場合、すべての変更を元に戻す
            DB::rollBack();

            // エラーログの記録や、ユーザーへのフィードバック
            Log::error('出品処理中にエラーが発生しました', [
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);   

            return redirect()->back()->with('error', '出品に失敗しました。もう一度お試しください');
        }
    }

}
