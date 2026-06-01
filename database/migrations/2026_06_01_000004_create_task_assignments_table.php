<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabela pivot: relaciona tarefas com as crianças responsáveis.
     * Uma tarefa pode ser atribuída a vários filhos (N:N).
     */
    public function up(): void
    {
        Schema::create('task_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('task_id')
                  ->constrained('tasks')
                  ->cascadeOnDelete();

            // user_id = a criança que recebeu a tarefa
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->timestamps();

            // Garante que a mesma tarefa não seja atribuída duas vezes ao mesmo usuário
            $table->unique(['task_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_assignments');
    }
};
