@extends('layouts.app')

@section('content')
    <h1 class="text-3xl font-bold mb-4">Editar Jogador</h1>
    <form action="{{ route('players.update', $player->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label for="name" class="block text-lg font-bold mb-2">Nome</label>
            <input type="text" name="name" value="{{ $player->name }}" required
                class="w-full p-2 rounded bg-slate-800 text-white border-none mb-4">
        </div>
        <div class="mb-4">
            <label min="1" max="5" for="level" class="block text-lg font-bold mb-2">Nível</label>
            <input type="number" name="level" min="1" max="5" value="{{ $player->level }}" required
                class="w-full p-2 rounded bg-slate-800 text-white border-none mb-4">
        </div>
        <div class="mb-4">
            <label for="is_goalkeeper" class="block text-lg font-bold mb-2">É goleiro?</label>
            <select name="is_goalkeeper" required class="w-full p-2 rounded bg-slate-800 text-white border-none mb-4">
                <option value="0" {{ $player->is_goalkeeper ? '' : 'selected' }}>Não</option>
                <option value="1" {{ $player->is_goalkeeper ? 'selected' : '' }}>Sim</option>
            </select>
        </div>
        <button type="submit"
            class="bg-navy-light hover:bg-navy-dark text-white font-bold py-2 px-4 rounded">Salvar</button>
    </form>
@endsection
