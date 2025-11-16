<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>coachtechフリマ</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>
<body>
    <div class="app">
        <header class="header">
        <a href="{{ route('index') }}">
            <img src="{{ asset('images/logo.svg') }}" alt="ロゴ" class="header-logo">
        </a>
        @if (!Route::is('login') &&
            !Route::is('register') &&
            !Route::is('verification.notice')
        )
            <div class="header-search">
                <form action="{{ route('index') }}" method="GET">
                    <input type="hidden" name="tab" value="{{ $tab ?? 'default' }}">
                    <input type="text" name="keyword" class="search-form" placeholder="なにをお探しですか？" value="{{ request('keyword') }}">
                </form>
            </div>
        @endif

        @if (!Route::is('login') &&
            !Route::is('register') &&
            !Route::is('verification.notice')
        )
        <ul class="header-nav">
            @if (!Auth::check())
                {{-- 未ログイン時 --}}
                <li class="header-nav__item">
                    <a class="header-nav__link" href="{{ route('login') }}">ログイン</a>
                </li>
                <li class="header-nav__item">
                    <a class="header-nav__link" href="{{ route('login') }}">マイページ</a>
                </li>
                <li class="header-nav__item">
                    <a class="header-nav__btn" href="{{ route('login') }}">出品</a>
                </li>
            @else
                {{-- ログイン時 --}}
                <li class="header-nav__item">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="logout-link">ログアウト</button>
                    </form>
                </li>

                <li class="header-nav__item">
                    <a class="header-nav__link" href="{{ route('mypage.index') }}">マイページ</a>
                </li>
                <li class="header-nav__item">
                    <a class="header-nav__btn" href="{{ route('sell.create') }}">出品</a>
                </li>
            @endif
        </ul>
    @endif
</header>
        <main class="main-content">
            @yield('content')
        </main>
    </div>
    @yield('script')
</body>

</html>
