{{-- resources/views/profile/form.blade.php --}}
@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile/form.css')}}">
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
    <div class="profile-form">
        <h1 class="profile-form__heading content__heading">プロフィール設定</h1>

        <div class="profile-form__inner"> 
            <form class="profile-form__form" method="POST" action="{{ route('profile.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="profile-form__group">
                    <div class="profile-container">
                        {{-- プロフィール画像プレビュー --}}
                        <div class="profile-image">
                            @if(!empty($profile->profile_image))
                                <img id="preview" src="{{ asset('storage/' . $profile->profile_image) }}" alt="プロフィール画像">
                            @else
                                <img id="preview" src="{{ asset('images/default.png') }}" alt="プロフィール画像">
                            @endif
                        </div>
                        {{-- ファイル選択ボタン --}}
                        <div>
                            <label for="profile_image" class="upload-label">画像を選択する</label>
                            <input type="file" id="profile_image" name="profile_image" class="upload-input" accept="image/*">
                        </div>                            
                        @error('profile_image')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="profile-form__group">
                    <label for="name" class="profile-form__label">ユーザー名</label>
                    <input id="name" 
                            type="text" 
                            class="profile-form__input" 
                            name="name" 
                            value="{{ old('name', $profile->name ?? '') }}">
                        @error('name')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                </div>

                <div class="profile-form__group">
                    <label for="postcode" class="profile-form__label">郵便番号</label>
                    <input id="postcode" 
                            type="text" 
                            class="profile-form__input" 
                            name="postcode" 
                            value="{{ old('postcode', $profile->postcode ?? '') }}" >
                    @error('postcode')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="profile-form__group">
                    <label for="address" class="profile-form__label">住所</label>
                    <input  id="address" 
                            type="text" 
                            class="profile-form__input" 
                            name="address" 
                            value="{{ old('address', $profile->address ?? '') }}">
                    @error('address')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="profile-form__group">
                    <label for="building" class="profile-form__label">建物</label>
                    <input id="building" 
                        type="text" 
                        class="profile-form__input" 
                        name="building" 
                        value="{{ old('building', $profile->building ?? '') }}" 
                        >
                    @error('building')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="profile-form__btn btn">更新する</button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
   document.getElementById('profile_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('preview');
        if (file) {
            preview.src = URL.createObjectURL(file);
        }
    });

</script>
@endsection