<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COACHTECH 新勤怠アプリ</title>
    <link rel="stylesheet" href="{{ asset('css/layouts/guest.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header-inner">
            <div class="header-logo">
                <a href="{{ route('attendance.index') }}">
                    <img src="{{ asset('img/logo.png') }}" alt="ロゴ">
                </a>
            </div>
            <nav class="header-nav">
                <ul class="header-nav-list">
                    @if (Auth::check())
                    <li class="header-nav-item">
                        <a href="{{ route('attendance.index') }}" class="header-nav-link">勤怠</a>
                    </li>
                    <li class="header-nav-item">
                        <a href="{{ route('attendance.list') }}" class="header-nav-link">勤怠一覧</a>
                    </li>
                    <li class="header__nav-item">
                        <a href="{{ route('stamp_correction_request.list') }}" class="header-nav-link">申請</a>
                    </li>
                    <li class="header-nav-item">
                        <form class="form" action="/logout" method="post">
                            @csrf
                            <button class="header-nav__button">ログアウト</button>
                        </form>
                    </li>
                    @endif
                </ul>
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>
</body>

</html>