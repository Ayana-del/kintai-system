@extends('layouts.guest')

@section('title', 'ログイン')

@section('content')
<link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">

<div class="login-container">
    <h1>ログイン</h1>

    @error('login_failed')
    <p class="error-login">{{ $message }}</p>
    @enderror

    <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf

        <div class="form-group">
            <label class="label-text">メールアドレス</label>
            <div class="input-group">
                <input type="email" name="email" value="{{ $errors->has('login_failed') ? '' : old('email') }}" required autofocus>
                @error('email')
                <p class="error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label class="label-text">パスワード</label>
            <div class="input-group">
                <input type="password" name="password" required>
                @error('password')
                <p class="error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <button type="submit" class="submit-btn">ログイン</button>
    </form>

    <div class="auth-link">
        <a href="{{ route('register') }}">会員登録はこちら</a>
    </div>
</div>
@endsection