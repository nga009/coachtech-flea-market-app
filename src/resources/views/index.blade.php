@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css')}}">
@endsection

@section('search')
<form method="GET" action="{{ route('items.index') }}">
    <input type="text" name="keyword" class="header__search" value="{{ old('keyword', $keyword ?? '') }}"  placeholder="なにをお探しですか？">
    <input type="hidden" name="tab" value="{{ $tab ?? '' }}" >
</form>
@endsection

@section('link')
<div class="header__link">
@auth
    <form action="/logout" method="post" class="header__form">
        @csrf
        <input class="header__input" type="submit" value="ログアウト">
    </form>
@else
    <div class="header__link--logout">
        <a href="/login" class="button-link" >ログイン</a>
    </div>
@endauth
    <div class="header__link--mypage">
        <a href="{{ route('profile.index')}}"" class="button-link" >マイページ</a>
    </div>
    <div class="header__link--sell button-wrapper">
        <a href="{{ route('sell.create')}}" class="button-link" >出品</a>
    </div>
</div>
@endsection

@section('content')
<div class="container">
    <div class="tab-container">
        <div class="tab-wrapper">
            {{-- タブナビゲーション --}}
            <ul class="nav-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $tab === 'all' ? 'active' : '' }}" 
                        href="{{ route('items.index', ['tab' => 'all', 'keyword' => old('keyword', $keyword ?? '')]) }}"
                        role="tab">
                        おすすめ
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $tab === 'mylist' ? 'active' : '' }}" 
                        href="{{ route('items.index', ['tab' => 'mylist', 'keyword' => old('keyword', $keyword ?? '')]) }}"
                        role="tab">
                        マイリスト
                    </a>
                </li>
            </ul>
        </div>
    </div>
    {{-- 商品グリッド --}}
    <div class="tab-content">
        <div class="item-list">
            @foreach($items as $item)
            <a href="/item/{{$item->id}}" class="button-wrapper item">
                @if($item->item_image)
                    <img src="{{ asset('storage/' . $item->item_image) }}" 
                    class="item-img-top" 
                    alt="{{ $item->name }}"
                    >
                @else
                    <div class="item-img-top">
                    </div>
                @endif
                <div class="item-body">
                    {{ $item->name }}
                    @if($item->is_sold)
                        <span class="item-sold">Sold</span>
                    @endif
                </div>
            </a>
            @endforeach
        </div>
    </div> 
</div>
@endsection
