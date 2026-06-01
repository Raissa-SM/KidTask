<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cria a tabela de famílias.
     * Deve rodar ANTES da alteração na tabela users (foreign key family_id).
     */
    public function up(): void
    {
        Schema::create('families', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // Código de convite único — usado para filhos entrarem na família
            $table->string('invite_code', 10)->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('families');
    }
};
