<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Histórico de todos os pontos ganhos e resgatados por cada criança.
     * Funciona como um extrato bancário de pontos.
     */
    public function up(): void
    {
        Schema::create('point_transactions', function (Blueprint $table) {
            $table->id();

            // Criança dona da transação
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Preenchido quando a transação originou de uma conclusão de tarefa aprovada
            $table->foreignId('task_completion_id')
                  ->nullable()
                  ->constrained('task_completions')
                  ->nullOnDelete();

            // Valor dos pontos (sempre positivo — o tipo define se ganhou ou gastou)
            $table->unsignedInteger('points');

            // earned = ganhou (tarefa aprovada), redeemed = resgatou (trocou por recompensa)
            $table->enum('type', ['earned', 'redeemed']);

            // Descrição legível (ex: "Concluiu: Lavar a louça" ou "Resgatou: Mesada")
            $table->string('description');

            $table->timestamps();

            // Índice para calcular saldo rapidamente
            $table->index(['user_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_transactions');
    }
};
