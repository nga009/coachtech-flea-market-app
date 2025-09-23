<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Http\Requests\ProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    // プロフィール画面（マイページ）
    public function index(Request $request) {
        $page = $request->get('page', 'sell');
        $keyword = $request->get('keyword', '');
        $user = auth()->user();
        $profile = auth()->user()->profile;

        $items = [];

        // 検索
        // 出品した商品タブの場合
        if ($page === 'sell') {
            $items = $user->sellingItems()->get();

        // 購入した商品タブの場合
        } else {
            $items = $user->purchasedItems()->get();

        }

        return view('profile.index', compact('user','profile', 'items', 'page'));

    }

    // プロフィール設定画面表示
    public function form()
    {
        $profile = auth()->user()->profile;
        
        return view('profile.form', compact('profile'));
    }

    // 登録更新処理
    public function store(ProfileRequest $request)
    {
        $user = auth()->user();
        $profile = $user->profile;
        $isEdit = !is_null($profile);

        $profileData = [
            'name' => $request->name,
            'postcode' => $request->postcode,
            'address' => $request->address,
            'building' => $request->building,
        ];

        if ($request->hasFile('profile_image')) {
            // 編集時は古い画像を削除
            if ($isEdit && $profile->profile_image) {
                Storage::disk('public')->delete($profile->profile_image);
            }
            $imagePath = $request->file('profile_image')->store('profile_images', 'public');
            $profileData['profile_image'] = $imagePath;
        }

        if ($isEdit) {
            // 更新処理
            $profile->update($profileData);
            $redirectRoute = 'profile.index';
        } else {
            // 新規作成処理
            $profileData['user_id'] = $user->id;
            Profile::create($profileData);
            $redirectRoute = 'items.index';
        }

        return redirect()->route($redirectRoute);
    }
}
