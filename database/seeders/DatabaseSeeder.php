<?php

namespace Database\Seeders;

use App\Models\Family;
use App\Models\Reward;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Cria dados de demonstração para facilitar o desenvolvimento e testes.
     *
     * Acesso após seed:
     *   Pai:  pai@kidtask.com  / password
     *   Filho: filho@kidtask.com / password
     */
    public function run(): void
    {
        // ── 1. Família de demonstração ────────────────────────────────────────
        $family = Family::create([
            'name'        => 'Família Silva',
            'invite_code' => 'DEMO01',
        ]);

        // ── 2. Usuário pai ────────────────────────────────────────────────────
        $parent = User::create([
            'name'      => 'Carlos Silva',
            'email'     => 'pai@kidtask.com',
            'password'  => Hash::make('password'),
            'family_id' => $family->id,
            'role'      => 'parent',
        ]);

        // ── 3. Usuário filho ──────────────────────────────────────────────────
        $child = User::create([
            'name'      => 'Lucas Silva',
            'email'     => 'filho@kidtask.com',
            'password'  => Hash::make('password'),
            'family_id' => $family->id,
            'role'      => 'child',
            'birthdate' => '2014-05-10',
        ]);

        // ── 4. Tarefas de demonstração ────────────────────────────────────────
        $tasks = [
            [
                'title'       => 'Arrumar a cama',
                'description' => 'Deixar a cama arrumada antes de sair do quarto.',
                'points'      => 5,
                'recurrence'  => 'daily',
            ],
            [
                'title'       => 'Lavar a louça',
                'description' => 'Lavar e secar toda a louça após o jantar.',
                'points'      => 10,
                'recurrence'  => 'daily',
            ],
            [
                'title'          => 'Tirar o lixo',
                'description'    => 'Levar o lixo para fora na segunda e quinta.',
                'points'         => 8,
                'recurrence'     => 'weekly',
                'recurrence_day' => 1, // segunda-feira
            ],
            [
                'title'      => 'Estudar 30 minutos',
                'description'=> 'Dedicar pelo menos 30 min aos estudos da escola.',
                'points'     => 15,
                'recurrence' => 'daily',
            ],
        ];

        foreach ($tasks as $taskData) {
            $task = Task::create(array_merge($taskData, [
                'family_id'  => $family->id,
                'created_by' => $parent->id,
                'is_active'  => true,
            ]));

            // Atribui cada tarefa ao filho de demonstração
            $task->assignedUsers()->attach($child->id);
        }

        // ── 5. Recompensas de demonstração ────────────────────────────────────
        Reward::create([
            'family_id'       => $family->id,
            'title'           => 'Mesada do mês',
            'description'     => 'R$ 30,00 de mesada ao atingir a meta mensal.',
            'points_required' => 200,
            'type'            => 'allowance',
        ]);

        Reward::create([
            'family_id'       => $family->id,
            'title'           => 'Escolher o filme do fim de semana',
            'description'     => 'Você escolhe o filme da sessão em família!',
            'points_required' => 50,
            'type'            => 'prize',
        ]);

        Reward::create([
            'family_id'       => $family->id,
            'title'           => 'Sorvete',
            'description'     => 'Uma ida à sorveteria com sabor a escolha.',
            'points_required' => 30,
            'type'            => 'prize',
        ]);
    }
}
