@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/login.css')}}">
@endsection

@section('content')
<div class="login-form">
    <h1 class="login-form__heading content__heading">ログイン</h1>
    
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    <div class="login-form__inner">
        <form class="login-form__form" method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="login-form__group">
                <label  class="login-form__label" for="email">メールアドレス</label>
                <input id="email" type="text" class="login-form__input" name="email" value="{{ old('email') }}">
                @error('email')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="login-form__group">
                <label class="login-form__label" for="password">パスワード</label>
                <input id="password" type="password" class="login-form__input" name="password">
                @error('password')
                    <div class="alert alert-danger">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="login-form__btn btn">ログインする</button>
            <p class="login-form__link">
                <a href="{{ route('register') }}">会員登録はこちら</a>
            </p>
        </form>
    </div>
</div>
@endsection