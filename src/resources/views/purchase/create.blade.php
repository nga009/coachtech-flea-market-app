@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase/create.css')}}">
@endsection

@section('search')
<form method="GET" action="{{ route('items.index') }}">
    <input type="text" name="keyword" class="header__search" value="{{ old('keyword', $keyword ?? '') }}"  placeholder="なにをお探しですか？">
    <input type="hidden" name="tab" value="{{ $tab ?? '' }}" >
</form>
@endsection

@section('link')
<div class="header__link">
    <form action="/logout" method="post" class="header__form">
        @csrf
        <input class="header__input" type="submit" value="ログアウト">
    </form>
    <div class="header__link--mypage">
        <a href="{{ route('profile.index')}}"" class="button-link" >マイページ</a>
    </div>
    </form>
    <div class="header__link--sell button-wrapper">
        <a href="{{ route('sell.create')}}" class="button-link" >出品</a>
    </div>
</div>
@endsection

@section('content')
<form method="POST" action="{{ route('purchase.store') }}">
    <div class="container">
        @csrf
        <div class="left-section">
            {{-- 商品情報 --}}
            <div class="item-info">
                <div class="item-image">
                    @if($item->item_image)
                        <img src="{{ asset('storage/' . $item->item_image) }}"  
                        class="item-img-top" 
                        alt="{{ $item->name }}"
                        >

                    @else
                        <div class="item-img-top">
                        </div>
                    @endif
                </div>
                <div class="item-details">
                    <div class="item-name">{{ $item->name }}</div>
                    <div class="item-price">{{ $item->formatted_price }}</div>
                </div>
            </div>
            <input type="hidden" name="item_id" value="{{ $item->id }}" >
            {{-- 支払い方法 --}}
            <div class="form-section">
                <h2 class="section-title">支払い方法</h2>
                <div class="form-group">
                    <div class="select-wrapper">
                        <select class="form-select" id="paymentMethod" name="payment_method">
                            <option value="" {{ $payment_method=='' ? 'selected' : '' }}>選択してください</option>
                            <option value="1" {{ $payment_method==1 ? 'selected' : '' }}>コンビニ払い</option>
                            <option value="2" {{ $payment_method==2 ? 'selected' : '' }}>カード支払い</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- 配送先 --}}
            <div class="form-section">
                <div class="address-header">
                    <h2 class="section-title">配送先</h2>
                    <a href="/purchase/address/{{$item->id}}" class="change-link">変更する</a>
                </div>
                <div class="address-info">
                    <div class="postal-code">〒<input type="text" name="shipping_postcode" class="address-info__postcode" value="{{ $address['shipping_postcode'] }}" readonly></div>
                    <div class="address-text">
                        <input type="text" name="shipping_address" class="address-info__address" value="{{ $address['shipping_address'] }}" readonly>
                        <input type="text" name="shipping_building" class="address-info__address" value="{{ $address['shipping_building'] }}" readonly>
                    </div>
                </div>
            </div>
        </div>


        <div class="right-section">
            {{-- 購入サマリー --}}
            <div class="summary-card">
                <div class="summary-row">
                    <span class="summary-label">商品代金</span>
                    <span class="summary-value">{{ $item->formatted_price }}</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">支払い方法</span>
                    <span class="summary-value" id="selectedPayment">コンビニ支払い</span>
                </div>
            </div>

            {{-- 購入ボタン --}}
            <button class="purchase-btn btn" name="purchaseButton" >
                購入する
            </button>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
    // 支払い方法の変更を監視
    document.getElementById('paymentMethod').addEventListener('change', function() {
        const selectedValue = this.value;
        const selectedText = this.options[this.selectedIndex].text;
        const paymentDisplay = document.getElementById('selectedPayment');
        
        if (selectedValue) {
            paymentDisplay.textContent = selectedText;
        } else {
            paymentDisplay.textContent = 'コンビニ払い';
        }
        
    });

    // ページ読み込み時の初期設定
    document.addEventListener('DOMContentLoaded', function() {
        
        // 支払い方法のプレースホルダー選択時も無効化
        document.getElementById('paymentMethod').selectedIndex = 0;
    });

    // フォームの自動保存
    function autoSave() {
        const formData = {
            payment_method: document.getElementById('paymentMethod').value,
            // 他のフォームデータも含める
        };
        
        // ローカルストレージに保存（一時的）
        localStorage.setItem('purchaseFormData', JSON.stringify(formData));
    }

    // フォームデータの復元
    function restoreFormData() {
        const saved = localStorage.getItem('purchaseFormData');
        if (saved) {
            const formData = JSON.parse(saved);
            if (formData.payment_method) {
                document.getElementById('paymentMethod').value = formData.payment_method;
                // change イベントを手動で発火
                document.getElementById('paymentMethod').dispatchEvent(new Event('change'));
            }
        }
    }

    // ページ読み込み時にフォームデータを復元
    document.addEventListener('DOMContentLoaded', restoreFormData);

    // フォーム変更時に自動保存
    document.getElementById('paymentMethod').addEventListener('change', autoSave);

</script>
@endsection
