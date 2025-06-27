<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Woordspel')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-900 min-h-screen">
<!-- Navigation -->
<nav class="bg-gray-800 shadow-sm border-b border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="text-xl font-bold text-blue-400">
                    🎯 Woordspel
                </a>
            </div>

            <div class="flex items-center space-x-4">
                @auth
                    <a href="{{ route('games.index') }}" class="text-gray-300 hover:text-blue-400">
                        Mijn Spellen
                    </a>
                    <a href="{{ route('friends.index') }}" class="text-gray-300 hover:text-blue-400">
                        Vrienden
                    </a>
                    <a href="{{ route('profile.show', Auth::user()) }}" class="text-gray-300 hover:text-blue-400">
                        Profiel
                    </a>
                    <a href="{{ route('settings.index') }}" class="text-gray-300 hover:text-blue-400">
                        Instellingen
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-300 hover:text-blue-400">
                            Uitloggen
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-gray-300 hover:text-blue-400">
                        Inloggen
                    </a>
                    <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        Registreren
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<!-- Flash Messages -->
@if(session('success'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <div class="bg-green-900 border border-green-700 text-green-300 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    </div>
@endif

@if(session('error'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <div class="bg-red-900 border border-red-700 text-red-300 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    </div>
@endif

@if($errors->any())
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <div class="bg-red-900 border border-red-700 text-red-300 px-4 py-3 rounded">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<!-- Main Content -->
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    @yield('content')
</main>
</body>
</html>
