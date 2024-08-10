@extends('layouts.app')

@section('content')
    <h1 class="text-3xl font-bold mb-4">Adicionar Jogador</h1>
    <form action="{{ route('players.store') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label for="name" class="block text-lg font-bold mb-2">Nome</label>
            <input type="text" name="name" required class="w-full p-2 rounded bg-slate-800 text-white border-none">
        </div>
        <div class="mb-4">
            <label for="level" class="block text-lg font-bold mb-2">Nível</label>
            <input type="number" name="level" min="1" max="5" required
                class="w-full p-2 rounded bg-slate-800 text-white border-none">
        </div>
        <div class="mb-4">
            <label for="is_goalkeeper" class="block text-lg font-bold mb-2">É goleiro?</label>
            <select name="is_goalkeeper" required class="w-full p-2 rounded bg-slate-800 text-white border-none">
                <option value="0">Não</option>
                <option value="1">Sim</option>
            </select>
        </div>
        <button type="submit"
            class="bg-slate-700 hover:bg-slate-600 text-white font-bold py-2 px-4 rounded">Adicionar</button>
    </form>
@endsection
