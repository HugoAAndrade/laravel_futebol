@extends('layout')

@section('content')
<h1 class="text-3xl font-bold mb-4">Jogadores</h1>

@if (session('success'))
<div class="bg-green-500 text-white p-4 rounded-lg mb-4">
    {{ session('success') }}
</div>
@endif

@if (session('error'))
<div class="bg-red-500 text-white p-4 rounded-lg mb-4">
    {{ session('error') }}
</div>
@endif

<div class="mb-4">
    <p class="text-lg font-bold">Total de jogadores confirmados: {{ $totalConfirmed }}</p>
    <p class="text-lg font-bold">Total de goleiros confirmados: {{ $totalGoalkeepersConfirmed }}</p>
    <p class="text-lg font-bold">Total de jogadores de linha confirmados: {{ $totalConfirmed - $totalGoalkeepersConfirmed }}</p>
</div>

<a href="{{ route('players.create') }}"
    class="bg-slate-700 hover:bg-slate-600 text-white font-bold py-2 px-4 rounded mb-4 inline-block">Adicionar
    Jogador
</a>

@php
$minPlayersPerTeam = 2; // Número mínimo de jogadores por time para formar 2 times
$canFormTwoTeams = ($totalConfirmed - $totalGoalkeepersConfirmed) >= ($minPlayersPerTeam - 1) * 2;
@endphp

@if (!$canFormTwoTeams)
<div class="bg-yellow-500 text-white p-4 rounded-lg mb-4">
    Não é possível formar dois times completos com os jogadores confirmados e o número atual de goleiros.
</div>
@endif

<form action="{{ route('players.drawTeams') }}" method="POST" class="mb-4" @if(!$canFormTwoTeams) style="display:none;" @endif>
    @csrf
    <label for="number_of_players_per_team" class="block text-lg font-bold mb-2">Número de jogadores por time:</label>
    <input min="6" type="number" name="number_of_players_per_team" required
        class="w-full p-2 rounded bg-slate-800 text-white border-none mb-4 focus:outline-none">
    <button type="submit" class="bg-slate-700 hover:bg-slate-600 text-white font-bold py-2 px-4 rounded">Sortear
        Times</button>
</form>

<ul>
    @foreach ($players as $player)
    <li class="bg-slate-700 p-4 rounded mb-2 flex justify-between">
        {{ $player->name }} (Nível: {{ $player->level }}, Goleiro: {{ $player->is_goalkeeper ? 'Sim' : 'Não' }},
        Confirmado: {{ $player->confirmed ? 'Sim' : 'Não' }})
        <div class="flex items-center">
            @if ($player->confirmed)

            <form action="{{ route('players.cancel', $player->id) }}" method="POST" class="inline-block ml-2">
                @csrf
                @method('POST')
                <button type="submit"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-1 px-2 rounded">Cancelar
                    Presença</button>
            </form>
            @else

            <form action="{{ route('players.confirm', $player->id) }}" method="POST" class="inline-block ml-2">
                @csrf
                <button type="submit"
                    class="bg-green-600 hover:bg-green-500 text-white font-bold py-1 px-2 rounded">Confirmar
                    Presença</button>
            </form>
            @endif

            <a href="{{ route('players.edit', $player->id) }}"
                class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-1 px-2 rounded inline-block ml-2">Editar</a>

            <form action="{{ route('players.destroy', $player->id) }}" method="POST" class="inline-block ml-2">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded">Deletar</button>
            </form>
        </div>
    </li>
    @endforeach
</ul>
@endsection