@extends('layouts.admin')

@section('title', '管理者ログイン')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/login.css') }}">
@endsection

@section('content')
<div class="auth">
    <div class="auth__container">
        <h1 class="auth__heading">管理者ログイン</h1>

        <div class="auth__card">
            <form action="{{ route('login') }}" method="post" class="auth__form">
                @csrf
                <div class="auth__group">
                    <label for="email" class="auth__label">メールアドレス</label>
                    <input type="email" name="email" id="email" class="auth__input" value="{{ old('email') }}">
                    @error('email')
                    <p class="auth__error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="auth__group">
                    <label for="password" class="auth__label">パスワード</label>
                    <input type="password" name="password" id="password" class="auth__input">
                    @error('password')
                    <p class="auth__error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="auth__actions">
                    <button type="submit" class="auth__button">管理者ログインする</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection