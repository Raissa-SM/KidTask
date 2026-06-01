<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Registra cada vez que uma criança marca uma tarefa como concluída.
     * O pai valida (aprova ou rejeita) e os pontos são creditados.
     */
    public function up(): void
    {
        Schema::create('task_completions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('task_id')
                  ->constrained('tasks')
                  ->cascadeOnDelete();

            // Criança que marcou como feita
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Momento em que a criança registrou a conclusão
            $table->datetime('completed_at');

            // Fluxo: pendente → aprovado ou rejeitado
            $table->enum('status', ['pending_validation', 'approved', 'rejected'])
                  ->default('pending_validation');

            // Pai/mãe que validou (preenchido após aprovação/rejeição)
            $table->foreignId('validated_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->datetime('validated_at')->nullable();

            // Observação opcional do validador (ex: "bom trabalho!" ou motivo da rejeição)
            $table->text('notes')->nullable();

            $table->timestamps();

            // Índices para as telas de validação (pai filtra por status e data)
            $table->index(['task_id', 'status']);
            $table->index('completed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_completions');
    }
};
