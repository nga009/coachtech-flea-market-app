@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase/address.css')}}">
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
    <div class="header__link--sell button-wrapper">
        <a href="{{ route('sell.create')}}" class="button-link" >出品</a>
    </div>
</div>
@endsection

@section('content')
<div class="address-form">
    <h1 class="address-form__heading content__heading">住所の変更</h1>
    
    <div class="address-form__inner">
        <form class="address-form__form" method="POST" action="{{ route('address.update') }}">
            @csrf
            
            <div class="address-form__group">
                <label class="address-form__label" for="name">郵便番号</label>
                <input type="text" class="address-form__input" name="shipping_postcode" 
                value="{{ old('shipping_postcode', $address['shipping_postcode']) }}">
                @error('shipping_postcode')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="address-form__group">
                <label class="address-form__label" for="shipping_address">住所</label>
                <input type="text" class="address-form__input" name="shipping_address" 
                value="{{ old('shipping_address', $address['shipping_address']) }}">
                @error('shipping_address')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="address-form__group">
                <label class="address-form__label" for="shipping_building">建物名</label>
                <input type="text" class="address-form__input" name="shipping_building" 
                value="{{ old('shipping_building', $address['shipping_building']) }}">
                @error('shipping_building')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <input type="hidden" name="item_id" value="{{ $item_id }}">
            <button type="submit" class="address-form__btn btn">更新する</button>
        </form>
    </div>
</div>
@endsection