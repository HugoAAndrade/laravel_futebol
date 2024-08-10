<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlayerController;

Route::get('/', function () {
    return redirect()->route('players.index');
});

Route::resource('players', PlayerController::class);
Route::post('players/confirm/{id}', [PlayerController::class, 'confirm'])->name('players.confirm');
Route::post('/players/{id}/cancel', [PlayerController::class, 'cancelPresence'])->name('players.cancel');
Route::post('players/draw-teams', [PlayerController::class, 'drawTeams'])->name('players.drawTeams');
