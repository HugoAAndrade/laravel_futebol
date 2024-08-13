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
        // Número de jogadores por time definido pelo usuário
        $numberOfPlayersPerTeam = $request->input('number_of_players_per_team');

        // Obter todos os jogadores confirmados
        $confirmedPlayers = Player::where('confirmed', true)->get();

        // Contar goleiros e jogadores de linha
        $goalkeepersCount = $confirmedPlayers->filter(fn($player) => $player->is_goalkeeper)->count();
        $fieldPlayersCount = $confirmedPlayers->filter(fn($player) => !$player->is_goalkeeper)->count();

        // Calcular o número mínimo de jogadores de linha necessários para formar dois times completos
        $requiredFieldPlayers = ($numberOfPlayersPerTeam - 1) * 2;

        // Verificar se há jogadores suficientes para formar pelo menos dois times completos
        if ($fieldPlayersCount < $requiredFieldPlayers) {
            return redirect()->back()->with('error', 'Não é possível formar dois times completos com o número atual de jogadores de linha e goleiros. Você precisa de pelo menos ' . $requiredFieldPlayers . ' jogadores de linha. Atualmente, há apenas ' . $fieldPlayersCount . ' jogadores de linha confirmados. Reduza o número de jogadores por time ou confirme mais jogadores.');
        }

        // Verificar se há jogadores suficientes para formar pelo menos dois times
        if ($confirmedPlayers->count() < $numberOfPlayersPerTeam * 2) {
            return redirect()->back()->with('error', 'O número máximo de jogadores por time deve ser menor ou igual a ' . floor($confirmedPlayers->count() / 2));
        }

        // Embaralhar os jogadores confirmados
        $confirmedPlayers = $confirmedPlayers->shuffle();

        // Separar os jogadores em goleiros e jogadores de linha
        $goalkeepersCollection = $confirmedPlayers->filter(fn($player) => $player->is_goalkeeper)->shuffle();
        $fieldPlayersCollection = $confirmedPlayers->filter(fn($player) => !$player->is_goalkeeper)->shuffle();

        // Inicializar os times
        $teams = [];
        $totalTeams = ceil($confirmedPlayers->count() / $numberOfPlayersPerTeam);

        for ($i = 1; $i <= $totalTeams; $i++) {
            $teams['Time ' . $i] = [];
        }

        // Preencher os times com goleiros primeiro, depois jogadores de linha
        foreach ($teams as $teamName => &$team) {
            // Adicionar um goleiro se houver
            if (!$goalkeepersCollection->isEmpty()) {
                $team[] = $goalkeepersCollection->shift();
            }
        }

        // Preencher os times com jogadores de linha
        foreach ($teams as $teamName => &$team) {
            // Preencher com jogadores de linha até atingir o limite
            while (count($team) < $numberOfPlayersPerTeam && !$fieldPlayersCollection->isEmpty()) {
                $team[] = $fieldPlayersCollection->shift();
            }
        }

        // Se ainda restarem jogadores de linha, distribuí-los no último time
        $lastTeamName = 'Time ' . $totalTeams;
        if (count($teams[$lastTeamName]) < $numberOfPlayersPerTeam) {
            while (!$fieldPlayersCollection->isEmpty() && count($teams[$lastTeamName]) < $numberOfPlayersPerTeam) {
                $teams[$lastTeamName][] = $fieldPlayersCollection->shift();
            }
        }

        // Qualquer goleiro restante é não alocado
        $remainingGoalkeepers = $goalkeepersCollection->all();

        // Exibir os resultados na view
        return view('teams', [
            'teams' => $teams,
            'remainingGoalkeepers' => $remainingGoalkeepers // Goleiros que ficaram de fora
        ]);
    }
}
