@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/register.css')}}">
@endsection

@section('content')
<div class="register-form">
    <h1 class="register-form__heading content__heading">会員登録</h1>
    
    <div class="register-form__inner">
        <form class="register-form__form" method="POST" action="{{ route('register') }}">
            @csrf
            
            <div class="register-form__group">
                <label class="register-form__label" for="name">ユーザー名</label>
                <input id="name" type="text" class="register-form__input" name="name" value="{{ old('name') }}">
                @error('name')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="register-form__group">
                <label class="register-form__label" for="email">メールアドレス</label>
                <input id="email" type="text" class="register-form__input" name="email" value="{{ old('email') }}">
                @error('email')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="register-form__group">
                <label class="register-form__label" for="password">パスワード</label>
                <input id="password" type="password" class="register-form__input" name="password">
                @error('password')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="register-form__group">
                <label class="register-form__label" for="password_confirmation">確認用パスワード</label>
                <input id="password_confirmation" type="password" class="register-form__input"  name="password_confirmation">
                @error('password_confirmation')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="register-form__btn btn">登録する</button>
            <p class="register-form__link">
                <a href="{{ route('login') }}">ログインはこちら</a>
            </p>
        </form>
    </div>
</div>
@endsection