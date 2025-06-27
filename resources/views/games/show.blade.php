@extends('layouts.app')

@section('title', 'Spel')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-gray-800 rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-blue-400">
                    {{ $game->player1->username }} vs {{ $game->player2->username }}
                </h1>
                <div class="text-sm text-gray-300">
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

            @if($game->status === 'waiting' && $game->player2_id === Auth::id())
                <div class="bg-blue-900 border border-blue-500 text-blue-300 px-4 py-3 rounded mb-4">
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
                <div class="mb-6">
                    <form method="POST" action="{{ route('games.guess', $game) }}" class="flex gap-2">
                        @csrf
                        <input type="text" name="guess" placeholder="Voer je woord in (5 letters)"
                               class="flex-1 px-3 py-2 border border-gray-600 bg-gray-700 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               maxlength="5" required>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                            Raden
                        </button>
                    </form>
                </div>
            @endif

            @if($game->status === 'finished')
                <div class="bg-gray-700 border border-gray-500 text-gray-200 px-4 py-3 rounded mb-4">
                    @if($game->winner_id)
                        <p><strong>Winnaar:</strong> {{ $game->winner->username }}</p>
                        <p><strong>Woord was:</strong> {{ $game->target_word }}</p>
                    @else
                        <p>Spel geëindigd zonder winnaar</p>
                        <p><strong>Woord was:</strong> {{ $game->target_word }}</p>
                    @endif
                </div>
            @endif

            <!-- Game Board -->
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <h3 class="font-bold text-gray-200 mb-2">{{ $game->player1->username }}</h3>
                    <div class="space-y-2">
                        @foreach($game->turns->where('player_id', $game->player1_id) as $turn)
                            <div class="flex gap-1">
                                @foreach(str_split($turn->guessed_word) as $index => $letter)
                                    <div class="w-10 h-10 border-2 flex items-center justify-center font-bold text-white
                                    {{ $turn->result[$index] === 'correct' ? 'bg-green-500 border-green-500' : '' }}
                                    {{ $turn->result[$index] === 'present' ? 'bg-yellow-500 border-yellow-500' : '' }}
                                    {{ $turn->result[$index] === 'absent' ? 'bg-gray-500 border-gray-500' : '' }}
                                ">
                                        {{ $letter }}
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>

                <div>
                    <h3 class="font-bold text-gray-200 mb-2">{{ $game->player2->username }}</h3>
                    <div class="space-y-2">
                        @foreach($game->turns->where('player_id', $game->player2_id) as $turn)
                            <div class="flex gap-1">
                                @foreach(str_split($turn->guessed_word) as $index => $letter)
                                    <div class="w-10 h-10 border-2 flex items-center justify-center font-bold text-white
                                    {{ $turn->result[$index] === 'correct' ? 'bg-green-500 border-green-500' : '' }}
                                    {{ $turn->result[$index] === 'present' ? 'bg-yellow-500 border-yellow-500' : '' }}
                                    {{ $turn->result[$index] === 'absent' ? 'bg-gray-500 border-gray-500' : '' }}
                                ">
                                        {{ $letter }}
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="border-t border-gray-600 pt-6">
                <h3 class="font-bold text-gray-200 mb-4">Reacties</h3>

                @if($game->status !== 'waiting')
                    <form method="POST" action="{{ route('comments.store', $game) }}" class="mb-4">
                        @csrf
                        <div class="flex gap-2">
                            <input type="text" name="content" placeholder="Voeg een reactie toe..."
                                   class="flex-1 px-3 py-2 border border-gray-600 bg-gray-700 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   required>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                                Verstuur
                            </button>
                        </div>
                    </form>
                @endif

                <div class="space-y-3">
                    @foreach($game->comments as $comment)
                        <div class="bg-gray-700 p-3 rounded-lg text-gray-200">
                            <div class="flex justify-between items-start">
                                <div>
                                    <strong>{{ $comment->author->username }}</strong>
                                    <span class="text-sm text-gray-400">{{ $comment->created_at->format('d-m-Y H:i') }}</span>
                                </div>
                                @if($comment->author_id === Auth::id())
                                    <form method="POST" action="{{ route('comments.destroy', $comment) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-600 text-sm">Verwijder</button>
                                    </form>
                                @endif
                            </div>
                            <p class="mt-1">{{ $comment->content }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
