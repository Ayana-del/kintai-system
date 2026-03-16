<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | COACHTECH</title>
    <link rel="stylesheet" href="{{ asset('css/layouts/admin.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <div class="header__logo">
                <img src="{{ asset('img/logo.png') }}" alt="COACHTECH">
            </div>

            @if (Auth::check() && Auth::user()->role === 1)
            <nav class="header__nav">
                <ul class="header__nav-list">
                    <li class="header__nav-item">
                        <a href="{{ route('admin.attendance.list') }}">勤怠一覧</a>
                    </li>
                    <li class="header__nav-item">
                        <a href="{{ route('admin.staff.list') }}">スタッフ一覧</a>
                    </li>
                    <li class="header__nav-item">
                        <a href="{{ route('admin.request.list') }}">申請一覧</a>
                    </li>
                    <li class="header__nav-item">
                        <form action="{{ route('logout') }}" method="post" class="header__form">
                            @csrf
                            <button type="submit" class="header__logout-button">ログアウト</button>
                        </form>
                    </li>
                </ul>
            </nav>
            @endif
        </div>
    </header>

    <main class="main">
        @yield('content')
    </main>
</body>

</html>