@extends('layouts.app')

@section('title', 'Mijn Spellen')

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-blue-400">Mijn Spellen</h1>
            <a href="{{ route('games.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                Nieuw Spel Starten
            </a>
        </div>

        <div class="grid gap-6">
            <!-- Active Games -->
            <div class="bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4 text-green-400">Actieve Spellen</h2>
                @forelse($activeGames as $game)
                    <div class="border-b border-gray-700 pb-4 mb-4 last:border-b-0 last:mb-0">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold text-gray-200">
                                    {{ $game->player1->username }} vs {{ $game->player2->username }}
                                </h3>
                                <p class="text-sm text-gray-400">
                                    Gestart: {{ $game->started_at ? $game->started_at->format('d-m-Y H:i') : $game->created_at->format('d-m-Y H:i') }}
                                </p>
                                @php
                                    $isUserTurn = $game->isUserTurn(Auth::id());
                                @endphp
                                <p class="text-sm {{ $isUserTurn ? 'text-green-400 font-semibold' : 'text-gray-500' }}">
                                    {{ $isUserTurn ? '🎯 Jouw beurt!' : '⏳ Wachten op tegenstander' }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    Jouw beurten: {{ $game->getUserTurnCount(Auth::id()) }}/6
                                </p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="bg-green-900 text-green-300 px-2 py-1 rounded-full text-xs">
                                    Actief
                                </span>
                                <a href="{{ route('games.show', $game) }}"
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                                    Spelen
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-400">Geen actieve spellen</p>
                @endforelse
            </div>

            <!-- Pending Games -->
            <div class="bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4 text-yellow-400">Uitnodigingen</h2>
                @forelse($pendingGames as $game)
                    <div class="border-b border-gray-700 pb-4 mb-4 last:border-b-0 last:mb-0">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold text-gray-200">
                                    {{ $game->player1->username }} vs {{ $game->player2->username }}
                                </h3>
                                <p class="text-sm text-gray-400">
                                    Uitgenodigd: {{ $game->created_at->format('d-m-Y H:i') }}
                                </p>
                                @if($game->player1_id === Auth::id())
                                    <p class="text-sm text-blue-400">Wachten op {{ $game->player2->username }}</p>
                                @else
                                    <p class="text-sm text-yellow-300">Uitnodiging van {{ $game->player1->username }}</p>
                                @endif
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="bg-yellow-900 text-yellow-300 px-2 py-1 rounded-full text-xs">
                                    Wachtend
                                </span>
                                <a href="{{ route('games.show', $game) }}"
                                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
                                    Bekijken
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-400">Geen openstaande uitnodigingen</p>
                @endforelse
            </div>

            <!-- Finished Games -->
            <div class="bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-xl font-bold mb-4 text-gray-300">Afgeronde Spellen</h2>
                @forelse($finishedGames as $game)
                    <div class="border-b border-gray-700 pb-4 mb-4 last:border-b-0 last:mb-0">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold text-gray-200">
                                    {{ $game->player1->username }} vs {{ $game->player2->username }}
                                </h3>
                                <p class="text-sm text-gray-400">
                                    Afgerond: {{ $game->ended_at->format('d-m-Y H:i') }}
                                </p>
                                @if($game->winner_id)
                                    <p class="text-sm {{ $game->winner_id === Auth::id() ? 'text-green-400' : 'text-red-400' }}">
                                        Winnaar: {{ $game->winner->username }}
                                        {{ $game->winner_id === Auth::id() ? '🏆' : '' }}
                                    </p>
                                @else
                                    <p class="text-sm text-gray-400">Geen winnaar</p>
                                @endif
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="bg-gray-700 text-gray-300 px-2 py-1 rounded-full text-xs">
                                    Afgerond
                                </span>
                                <a href="{{ route('games.show', $game) }}"
                                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
                                    Bekijken
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-400">Nog geen afgeronde spellen</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
