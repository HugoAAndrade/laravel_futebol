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
    public function destroy($id)
    {
        $player = Player::findOrFail($id);
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
        $confirmedPlayers = Player::where('confirmed', true)->get();

        // Separar goleiros e jogadores de linha
        $goalkeepers = $confirmedPlayers->filter(function ($player) {
            return $player->is_goalkeeper;
        })->sortByDesc('level'); // Ordenar por nível para balancear os times

        $fieldPlayers = $confirmedPlayers->filter(function ($player) {
            return !$player->is_goalkeeper;
        })->sortByDesc('level'); // Ordenar por nível para balancear os times

        // Verificar se há jogadores confirmados suficientes para formar pelo menos dois times
        if ($confirmedPlayers->count() < $numberOfPlayersPerTeam * 2) {
            return redirect()->back()->with('error', 'Número insuficiente de jogadores confirmados.');
        }

        // Calcular o número de times necessários
        $totalTeams = intdiv($confirmedPlayers->count(), $numberOfPlayersPerTeam);
        $remainingPlayersCount = $confirmedPlayers->count() % $numberOfPlayersPerTeam;

        $teams = [];

        // Inicializar os times
        for ($i = 1; $i <= $totalTeams; $i++) {
            $teams['Time ' . $i] = [];
        }

        // Distribuir goleiros balanceadamente entre os times
        foreach (array_keys($teams) as $team) {
            if (!$goalkeepers->isEmpty()) {
                $teams[$team][] = $goalkeepers->shift();
            }
        }

        // Distribuir os jogadores de linha balanceadamente entre os times
        foreach ($teams as $teamName => &$team) {
            while (count($team) < $numberOfPlayersPerTeam && !$fieldPlayers->isEmpty()) {
                $player = $fieldPlayers->shift();
                $team[] = $player;
            }
        }

        // Se ainda houver jogadores restantes, distribuí-los nos times existentes, respeitando o limite por time
        if (!$fieldPlayers->isEmpty()) {
            foreach ($teams as &$team) {
                if (count($team) < $numberOfPlayersPerTeam && !$fieldPlayers->isEmpty()) {
                    $team[] = $fieldPlayers->shift();
                }
            }
        }

        // Adicionar os jogadores restantes ao último time, se ainda houver jogadores restantes
        if (!$fieldPlayers->isEmpty()) {
            $lastTeam = 'Time ' . ($totalTeams + 1);
            $teams[$lastTeam] = $fieldPlayers->all();
        }

        // Validar se há mais de um goleiro em algum time, o que não é permitido
        foreach ($teams as $teamName => &$team) {
            $goalkeepersInTeam = array_filter($team, function ($player) {
                return $player->is_goalkeeper;
            });

            if (count($goalkeepersInTeam) > 1) {
                return redirect()->back()->with('error', 'Cada time só pode ter um goleiro.');
            }
        }

        return view('teams', ['teams' => $teams]);
    }
}
