<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Player;

class PlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cria 25 jogadores aleatórios
        Player::factory(25)->create();

        // Exemplo de criação de um jogador específico
        // Player::factory()->create([
        //     'name' => 'João Goleiro',
        //     'level' => 4,
        //     'is_goalkeeper' => true,
        //     'confirmed' => true,
        // ]);
    }
}
