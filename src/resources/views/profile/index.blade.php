@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile/index.css')}}">
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
<div class="container">
    <div class="profile-container">
        {{-- プロフィール画像プレビュー --}}
        <div class="profile-image">
            @if(!empty($profile->profile_image))
                <img id="preview" src="{{ asset('storage/' . $profile->profile_image) }}" alt="プロフィール画像">
            @else
                <img id="preview" src="{{ asset('images/default.png') }}" alt="プロフィール画像">
            @endif
        </div>
        <div>
            <p>{{ $user->name }}</p>
        </div>
        {{-- 編集画面へボタン --}}
        <div class="edit-link button-wrapper">
            <a href="{{ route('profile.form')}}" class="button-link" >プロフィールを編集</a>
        </div>                            
    </div>
    <div class="tab-container">
        <div class="tab-wrapper">
            {{-- タブナビゲーション --}}
            <ul class="nav-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $page === 'sell' ? 'active' : '' }}" 
                        href="{{ route('profile.index', ['page' => 'sell']) }}"
                        role="tab">
                        出品した商品
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $page === 'buy' ? 'active' : '' }}" 
                        href="{{ route('profile.index', ['page' => 'buy']) }}"
                        role="tab">
                        購入した商品
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
                @if (substr($item->item_image, 0, 4) !== 'http')
                    <img src="{{ asset('storage/' . $item->item_image) }}" 
                @else                        
                    <img src="{{ $item->item_image }}" 
                @endif
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
