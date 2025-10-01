<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <link href="{{ asset('css/daygrid.css') }}" rel="stylesheet">

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

</head>

<body class="">
    <header class="">
        <div class="p-6 items-center justify-between w-full bg-green-500 text-white">
            <h1 class="xl:text-2xl">診察予約フォーム</h1>
        </div>
        <div>
            <nav>
                <ul class="flex px-6 gap-1 mt-3">
                    <li class="p-1 border">
                        <a href="">予約一覧</a>
                    </li>
                    <li class="p-1 border">
                        <a href="">営業日カレンダー</a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="max-w-screen-lg mx-auto px-6 pb-10">
        @yield('content')
    </main>

</body>

</html>
