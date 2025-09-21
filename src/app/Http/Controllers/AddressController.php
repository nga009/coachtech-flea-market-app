<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddressRequest;

class AddressController extends Controller
{
    // 住所変更画面　表示
    public function edit(Request $request, $item_id)
    {
        $profile = auth()->user()->profile;

        // 住所　初期表示はプロフィールの住所
        $address = $request->session()->get('shipping_address', [
            'shipping_postcode' => $profile->postcode,
            'shipping_address' => $profile->address,
            'shipping_building' => $profile->building,
        ]);

        return view('purchase.address', compact('item_id', 'address'));

    }

    // 住所変更画面　更新
    public function update(AddressRequest $request)
    {

        // 入力内容をセッションに保存
        $request->session()->put('shipping_address', $request->validated());

        return redirect()->route('purchase.create', [
                            'item_id' => $request->item_id
                        ]);
    }
}
