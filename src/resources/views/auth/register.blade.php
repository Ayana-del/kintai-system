@extends('layouts.guest')

@section('title', '会員登録')

@section('content')
<link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">

<div class="register-container">
    <h1>会員登録</h1>

    <form method="POST" action="{{ route('register') }}" novalidate>
        @csrf

        <div class="form-group">
            <label class="label-text">名前</label>
            <div class="input-group"> <input type="text" name="name" value="{{ old('name') }}" required autofocus>
                @error('name')
                <p class="error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label class="label-text">メールアドレス</label>
            <div class="input-group">
                <input type="email" name="email" value="{{ old('email') }}" required>
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

        <div class="form-group">
            <label class="label-text">パスワード確認</label>
            <div class="input-group">
                <input type="password" name="password_confirmation" required>
            </div>
        </div>

        <button type="submit">会員登録</button>
    </form>

    <div class="login-link">
        <a href="{{ route('login') }}">ログインはこちら</a>
    </div>
</div>
@endsection