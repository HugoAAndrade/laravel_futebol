<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $players = Player::all();

        $confirmedPlayers = $players->where('confirmed', true);
        $totalConfirmed = $confirmedPlayers->count();
        $totalGoalkeepersConfirmed = $confirmedPlayers->where('is_goalkeeper', true)->count();

        return view('players.index', [
            'players' => $players,
            'totalConfirmed' => $totalConfirmed,
            'totalGoalkeepersConfirmed' => $totalGoalkeepersConfirmed,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('players.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'level' => 'required|integer|min:1|max:5',
            'is_goalkeeper' => 'required|boolean',
        ]);

        // Remover o campo `_token`
        $playerData = $request->except('_token');

        Player::create($playerData);

        return redirect()->route('players.index')->with('success', 'Player created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Player $player)
    {
        return view('players.show', compact('player'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Player $player)
    {
        return view('players.edit', compact('player'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Player $player)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'level' => 'required|integer|min:1|max:5',
            'is_goalkeeper' => 'required|boolean',
            'confirmed' => 'sometimes|boolean',
        ]);

        $player->update($request->except('_token'));

        return redirect()->route('players.index')->with('success', 'Player updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Player $player)
    {
        $player->delete();

        return redirect()->route('players.index')->with('success', 'Player deleted successfully.');
    }

    /**
     * Confirm the presence of the specified player.
     */
    public function confirm($id)
    {
        $player = Player::findOrFail($id);
        $player->confirmed = true;
        $player->save();

        return redirect()->route('players.index')->with('success', 'Player confirmed successfully.');
    }

    public function cancelPresence($id)
    {
        $player = Player::findOrFail($id);
        $player->confirmed = false;
        $player->save();

        return redirect()->route('players.index')->with('success', 'Presença cancelada com sucesso.');
    }

    /**
     * Draw teams based on confirmed players.
     */

    public function drawTeams(Request $request)
    {
        // Obter o número de jogadores por time a partir do input
        $numberOfPlayersPerTeam = $request->input('number_of_players_per_team');

        // Obter todos os jogadores confirmados
        $confirmedPlayers = Player::where('confirmed', true)->get()->shuffle();

        // Separar goleiros e jogadores de linha
        $goalkeepers = $confirmedPlayers->filter(function ($player) {
            return $player->is_goalkeeper;
        })->shuffle();

        $fieldPlayers = $confirmedPlayers->filter(function ($player) {
            return !$player->is_goalkeeper;
        })->shuffle();

        // Verificar se há jogadores confirmados suficientes para formar pelo menos dois times
        if ($confirmedPlayers->count() < $numberOfPlayersPerTeam * 2) {
            return redirect()->back()->with('error', 'Número insuficiente de jogadores confirmados.');
        }

        // Calcular o número de times necessários
        $totalTeams = intdiv($confirmedPlayers->count(), $numberOfPlayersPerTeam);
        $remainingPlayersCount = $confirmedPlayers->count() % $numberOfPlayersPerTeam;

        $teams = [];

        // Inicializar os times completos
        for ($i = 1; $i <= $totalTeams; $i++) {
            $teams['team' . $i] = [];
        }

        // Garantir que cada time tenha pelo menos um goleiro
        foreach (array_keys($teams) as $team) {
            if (!$goalkeepers->isEmpty()) {
                $teams[$team][] = $goalkeepers->shift();
            }
        }

        // Preencher os times com o restante dos jogadores de linha
        foreach ($teams as $teamName => &$team) {
            while (count($team) < $numberOfPlayersPerTeam && !$fieldPlayers->isEmpty()) {
                $team[] = $fieldPlayers->shift();
            }
        }

        // Adicionar os jogadores restantes ao último time, se houver
        if (!$fieldPlayers->isEmpty()) {
            $lastTeam = 'team' . ($totalTeams + 1);
            $teams[$lastTeam] = $fieldPlayers->all();
        }

        return view('teams', ['teams' => $teams]);
    }
}
