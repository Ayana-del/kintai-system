<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atte</title>
    <link rel="stylesheet" href="{{ asset('css/layouts/guest.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header-inner">
            <div class="header-logo">
                <img src="{{ asset('img/logo.png') }}" alt="ロゴ">
            </div>
            <nav class="header-nav">
                <ul class="header-nav-list">
                    @if (Auth::check())
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