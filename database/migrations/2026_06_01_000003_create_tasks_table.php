<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cria a tabela de tarefas/afazeres.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            // A qual família esta tarefa pertence
            $table->foreignId('family_id')
                  ->constrained('families')
                  ->cascadeOnDelete();

            // Qual pai/mãe criou a tarefa
            $table->foreignId('created_by')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->string('title');
            $table->text('description')->nullable();

            // Pontos que a criança ganha ao concluir (mínimo 1)
            $table->unsignedSmallInteger('points')->default(1);

            // Recorrência da tarefa
            $table->enum('recurrence', ['none', 'daily', 'weekly', 'monthly'])->default('none');

            // Para recorrência semanal: 0=Dom, 1=Seg, ..., 6=Sáb
            // Para recorrência mensal: 1–31 (dia do mês)
            $table->unsignedTinyInteger('recurrence_day')->nullable();

            // Para tarefas sem recorrência (evento único)
            $table->date('due_date')->nullable();

            // Horário do lembrete/notificação
            $table->time('reminder_time')->nullable();

            // Soft-disable: desativa sem apagar o histórico
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Índices para filtros frequentes
            $table->index(['family_id', 'is_active']);
            $table->index('due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
