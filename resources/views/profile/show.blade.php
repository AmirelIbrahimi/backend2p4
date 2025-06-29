@extends('layouts.app')

@section('title', 'Spel')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-gray-900 rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-white">
                    {{ $game->player1->username }} vs {{ $game->player2->username }}
                </h1>
                <div class="text-sm text-gray-400">
                    Status:
                    <span class="font-semibold
                        {{ $game->status === 'waiting' ? 'text-yellow-400' : '' }}
                        {{ $game->status === 'active' ? 'text-green-400' : '' }}
                        {{ $game->status === 'finished' ? 'text-gray-400' : '' }}
                    ">
                        {{ ucfirst($game->status) }}
                    </span>
                </div>
            </div>

            @foreach (['success' => 'green', 'error' => 'red', 'info' => 'blue'] as $type => $color)
                @if(session($type))
                    <div class="bg-{{ $color }}-900 border border-{{ $color }}-700 text-{{ $color }}-300 px-4 py-3 rounded mb-4">
                        {{ session($type) }}
                    </div>
                @endif
            @endforeach

            @if($game->status === 'waiting' && $game->player2_id === Auth::id())
                <div class="bg-blue-900 border border-blue-700 text-blue-300 px-4 py-3 rounded mb-4">
                    <p>Je bent uitgenodigd voor dit spel!</p>
                    <form method="POST" action="{{ route('games.accept', $game) }}" class="mt-2">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                            Spel Accepteren
                        </button>
                    </form>
                </div>
            @endif

            @if($game->status === 'active')
                @php
                    $isUserTurn = $game->isUserTurn(Auth::id());
                    $userTurnCount = $game->getUserTurnCount(Auth::id());
                @endphp

                @if($isUserTurn && $userTurnCount < 6)
                    <div class="mb-6 bg-green-900 border border-green-700 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-green-300 mb-3">🎯 Het is jouw beurt!</h3>
                        <p class="text-sm text-green-200 mb-3">Beurt {{ $userTurnCount + 1 }} van 6</p>
                        <form method="POST" action="{{ route('games.guess', $game) }}" class="flex gap-2">
                            @csrf
                            <input type="text" name="guess" placeholder="Voer je woord in (5 letters)"
                                   class="flex-1 px-3 py-2 border border-gray-600 bg-gray-800 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 uppercase"
                                   maxlength="5" pattern="[A-Za-z]{5}" required>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                                Raden
                            </button>
                        </form>
                        <p class="text-xs text-gray-400 mt-2">Tip: Voer een Nederlands woord van 5 letters in</p>
                    </div>
                @else
                    <div class="mb-6 bg-gray-800 border border-gray-700 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-gray-200 mb-2">⏳ Wachten op tegenstander</h3>
                        <p class="text-sm text-gray-400">
                            @if($userTurnCount >= 6)
                                Je hebt al je 6 beurten gebruikt. Wachten op de tegenstander.
                            @else
                                Het is de beurt van je tegenstander.
                            @endif
                        </p>
                    </div>
                @endif
            @endif

            @if($game->status === 'finished')
                <div class="bg-gray-800 border border-gray-700 text-gray-200 px-4 py-3 rounded mb-4">
                    @if($game->winner_id)
                        <p><strong>🏆 Winnaar:</strong> {{ $game->winner->username }}</p>
                        <p><strong>📝 Woord was:</strong> <span class="font-mono text-lg text-white">{{ $game->target_word }}</span></p>
                    @else
                        <p>🤝 Spel geëindigd zonder winnaar</p>
                        <p><strong>📝 Woord was:</strong> <span class="font-mono text-lg text-white">{{ $game->target_word }}</span></p>
                    @endif
                </div>
            @endif

            <!-- Game Board -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                @foreach([$game->player1_id, $game->player2_id] as $playerId)
                    @php
                        $player = $playerId === $game->player1_id ? $game->player1 : $game->player2;
                        $playerTurns = $game->turns->where('player_id', $playerId);
                        $isUserTurnForPlayer = $game->status === 'active' && $game->isUserTurn($playerId);
                    @endphp
                    <div class="bg-gray-800 p-4 rounded-lg">
                        <h3 class="font-bold mb-3 text-center text-white">
                            {{ $player->username }}
                            @if($isUserTurnForPlayer)
                                <span class="text-green-400">🎯</span>
                            @endif
                        </h3>
                        <div class="space-y-2">
                            @for($i = 0; $i < 6; $i++)
                                @php $turn = $playerTurns->skip($i)->first(); @endphp
                                <div class="flex gap-1 justify-center">
                                    @if($turn)
                                        @foreach(str_split($turn->guessed_word) as $index => $letter)
                                            <div class="w-10 h-10 border-2 flex items-center justify-center font-bold text-white rounded
                                                {{ $turn->result[$index] === 'correct' ? 'bg-green-500 border-green-500' : '' }}
                                                {{ $turn->result[$index] === 'present' ? 'bg-yellow-500 border-yellow-500' : '' }}
                                                {{ $turn->result[$index] === 'absent' ? 'bg-gray-600 border-gray-600' : '' }}
                                            ">
                                                {{ $letter }}
                                            </div>
                                        @endforeach
                                    @else
                                        @for($j = 0; $j < 5; $j++)
                                            <div class="w-10 h-10 border-2 border-gray-600 bg-gray-700 rounded"></div>
                                        @endfor
                                    @endif
                                </div>
                            @endfor
                            <p class="text-xs text-center text-gray-400">
                                {{ $playerTurns->count() }}/6 beurten gebruikt
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Game Legend -->
            <div class="mb-6 bg-gray-800 p-4 rounded-lg">
                <h4 class="font-semibold text-white mb-2">Kleurenbetekenis:</h4>
                <div class="flex flex-wrap gap-4 text-sm text-gray-300">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 bg-green-500 rounded border border-green-500"></div>
                        <span>Correcte letter op juiste plaats</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 bg-yellow-500 rounded border border-yellow-500"></div>
                        <span>Letter aanwezig maar op verkeerde plaats</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 bg-gray-600 rounded border border-gray-600"></div>
                        <span>Letter niet aanwezig in het woord</span>
                    </div>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="bg-gray-900 rounded-lg shadow p-6">
                <h3 class="text-xl font-bold mb-4 text-white">Reacties</h3>

                @forelse($game->comments()->with('user')->latest()->get() as $comment)
                    <div class="border-b border-gray-700 py-2">
                        <p class="text-sm text-gray-400"><strong>{{ $comment->user->username }}</strong> zegt:</p>
                        <p class="text-gray-300">{{ $comment->content }}</p>
                        <p class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</p>
                    </div>
                @empty
                    <p class="text-gray-500">Nog geen reacties.</p>
                @endforelse

                @auth
                    <form method="POST" action="{{ route('games.comments.store', $game) }}" class="mt-4">
                        @csrf
                        <textarea name="content" rows="3" placeholder="Laat een reactie achter..."
                                  class="w-full border border-gray-600 bg-gray-800 text-white rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required></textarea>
                        <button type="submit" class="mt-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                            Plaats Reactie
                        </button>
                    </form>
                @else
                    <p class="mt-4 text-gray-400">Je moet ingelogd zijn om een reactie te plaatsen.</p>
                @endauth
            </div>
        </div>
    </div>
@endsection
