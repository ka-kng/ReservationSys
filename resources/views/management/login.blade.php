<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-screen flex items-center justify-center">
    <div class="w-full max-w-xl mx-auto p-4 md:border rounded md:shadow">
        <h1 class="text-center text-3xl font-bold mb-4">ログイン</h1>

        @if (session('status'))
            <div class="bg-green-100 p-2 mb-4">{{ session('status') }}</div>
        @endif

        <form class="p-4 border rounded shadow" method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-4">
                <label for="login_id" class="block font-medium">ログインID</label>
                <input id="login_id" type="text" name="login_id" value="{{ old('login_id') }}" autofocus
                    class="w-full border p-2 rounded">
                @error('login_id')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password" class="block font-medium">パスワード</label>
                <input id="password" type="password" name="password" class="w-full border p-2 rounded">
                @error('password')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600">
                ログイン
            </button>

        </form>


    </div>
</body>

</html>
