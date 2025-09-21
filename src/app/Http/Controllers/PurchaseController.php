<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Purchase;
use App\Http\Requests\PurchaseRequest;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PurchaseController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('stripe.secret'));
    }

    // 購入画面 表示
    public function create(Request $request,$item_id)
    {
        $item = Item::find($item_id);
        $profile = auth()->user()->profile;

        $address = $request->session()->get('shipping_address', [
            'shipping_postcode' => $profile->postcode,
            'shipping_address' => $profile->address,
            'shipping_building' => $profile->building,
        ]);
        // 支払方法
        $payment_method = $request->session()->get('payment_method','');

        return view('purchase.create', compact('item', 'payment_method', 'address'));
    }

    // 購入画面 購入
    public function store(PurchaseRequest $request)
    {

        try {
            DB::beginTransaction();

            // 購入登録
            $purchase = Purchase::create([
                'item_id' => $request->item_id,
                'buyer_id' => auth()->user()->id,
                'shipping_postcode' => $request->shipping_postcode,
                'shipping_address' => $request->shipping_address,
                'shipping_building' => $request->shipping_building,
                'payment_method' => $request->payment_method,
                'stripe_session_id' => '', // 後で更新
            ]);

            //  商品情報取得
            $item = Item::find($request->item_id);

            // 支払方法設定
            $payment_method_str = ['konbini'];
            if ($request->payment_method == 2) 
            {
                $payment_method_str = ['card'];
            }

            // Stripe Checkoutセッション作成
            $session = Session::create([
                'payment_method_types' => $payment_method_str,
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'jpy',
                            'product_data' => [
                                'name' => $item->name,
                                'images' => $item->image_url ? [$item->image_url] : [],
                            ],
                            'unit_amount' => (int)$item->price,
                        ],
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'payment',
                'success_url' => route('purchase.success', ['purchase' => $purchase->id]),
                'cancel_url' => route('purchase.cancel', ['item' => $item->id]),

                'metadata' => [
                    'purchase_id' => $purchase->id,
                    'item_id' => $item->id,
                    'user_id' => Auth::id(),
                ],
            ]);

            // セッションIDを更新
            $purchase->update(['stripe_session_id' => $session->id]);

            // 商品のis_sold更新
            $itemData = [
                'is_sold' => 1,
            ];
            $item->update($itemData);

            // コミット
            DB::commit();

            // 商品一覧画面へ
            return redirect($session->url);

        } catch (\Exception $e) {
            // 例外が発生した場合、すべての変更を元に戻す
            DB::rollBack();

            // エラーログの記録や、ユーザーへのフィードバック
            Log::error('購入処理中にエラーが発生しました', [
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);   

            return redirect()->back();
        }
    }

   /**
     * 購入成功処理
     */
    public function success(Purchase $purchase)
    {
        try {
            // セッション情報を取得
            $session = Session::retrieve($purchase->stripe_session_id);
            
            if ($session->payment_status === 'paid') {
                $purchase->update(['status' => 'completed']);
                
                return redirect()->route('items.index', $purchase->item);
            }
            
            return redirect()->route('purchase.create', ['item_id' => $item->id])
                ->with('error', '決算が完了していません');
                
        } catch (\Exception $e) {
            Log::error('Purchase success handling failed: ' . $e->getMessage());
            return rredirect()->route('purchase.create', ['item_id' => $item->id])
                ->with('error', '購入処理でエラーが発生しました');
        }
    }

    /**
     * 購入キャンセル処理
     */
    public function cancel(Item $item)
    {
        return redirect()->route('purchase.create', ['item_id' => $item->id])
            ->with('info', '購入をキャンセルしました');
    }
}
