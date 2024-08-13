@extends('layout')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-4 text-center">Times Sorteados</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
        @foreach ($teams as $teamName => $players)
        @php
        $hasGoalkeeper = count(array_filter($players, fn($player) => $player->is_goalkeeper));
        @endphp
        @if ($hasGoalkeeper && count($players) > 1)
        <div class="bg-slate-600 shadow-md rounded-lg p-4">
            <h2 class="text-2xl font-semibold mb-2 text-white">{{ ucfirst($teamName) }}</h2>
            <ul class="list-disc pl-5">
                @foreach ($players as $player)
                <li class="mb-1">
                    <span class="font-medium">{{ $player->name }}</span> - Nível: {{ $player->level }},
                    Goleiro: {{ $player->is_goalkeeper ? 'Sim' : 'Não' }}
                </li>
                @endforeach
            </ul>
        </div>
        @endif
        @endforeach
    </div>
</div>
@endsection