<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\SellController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\AddressController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// 公開ページ
Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('items.show');

// メール認証誘導画面
Route::get('/email/verify/notice', [VerificationController::class, 'notice'])
    ->name('verification.notice');

// メール認証済みユーザーのみ
Route::middleware(['auth', 'verified'])->group(function () {
    // プロフィール画面
    Route::get('/mypage', [ProfileController::class, 'index'])->name('profile.index');

    // プロフィール編集画面
    Route::get('/mypage/profile', [ProfileController::class, 'form'])->name('profile.form');
    Route::post('/mypage', [ProfileController::class, 'store'])->name('profile.store');

    // 出品画面
    Route::get('/sell', [SellController::class, 'create'])->name('sell.create');
    Route::post('/sell', [SellController::class, 'store'])->name('sell.store');

    // 商品詳細画面 コメント登録
    Route::post('item/{item_id}/comments', [CommentController::class, 'store'])
    ->name('comments.store');
        
    // 商品詳細画面 お気に入り追加
    Route::post('/item/{item}/favorite', [FavoriteController::class, 'toggle'])->name('favorite.toggle');

    // 購入画面
    Route::get('purchase/{item_id}', [PurchaseController::class, 'create'])->name('purchase.create');
    Route::post('purchase', [PurchaseController::class, 'store'])->name('purchase.store');
    // 購入成功
    Route::get('purchase/{purchase}/success', [PurchaseController::class, 'success'])
        ->name('purchase.success');
    // 購入キャンセル
    Route::get('purchase/{item}/cancel', [PurchaseController::class, 'cancel'])
        ->name('purchase.cancel');

    // 住所変更画面
    Route::get('purchase/address/{item_id}', [AddressController::class, 'edit'])->name('address.edit');
    Route::post('purchase/address', [AddressController::class, 'update'])->name('address.update');

});