@extends('layouts.guest')

@section('title', 'メール認証')

@section('content')
<link rel="stylesheet" href="{{ asset('css/auth/verify-email.css') }}">

<div class="verify-container">
    <div class="message-group">
        <p class="message-text">
            登録していただいたメールアドレスに認証メールを送付しました。<br>
            メール認証を完了してください。
        </p>
    </div>

    <div class="action-group">
        <button type="button" class="verify-btn" onclick="window.open('http://localhost:8025', '_blank')">確認はこちらから</button>

        <div class="resend-link-container">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="link-button">認証メールを再送する</button>
            </form>
        </div>

        @if (session('status') == 'verification-link-sent')
        <p class="status-message">新しい認証メールを再送信しました。</p>
        @endif
    </div>
</div>
@endsection