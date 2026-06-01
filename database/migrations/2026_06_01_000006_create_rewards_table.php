<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Recompensas que os filhos podem resgatar com seus pontos acumulados.
     */
    public function up(): void
    {
        Schema::create('rewards', function (Blueprint $table) {
            $table->id();

            $table->foreignId('family_id')
                  ->constrained('families')
                  ->cascadeOnDelete();

            $table->string('title');
            $table->text('description')->nullable();

            // Custo em pontos para resgatar esta recompensa
            $table->unsignedInteger('points_required');

            // allowance = mesada (dinheiro real), prize = prêmio (objeto, passeio, etc.)
            $table->enum('type', ['allowance', 'prize'])->default('prize');

            $table->timestamps();

            $table->index('family_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rewards');
    }
};
