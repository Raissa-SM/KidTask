<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adiciona campos de família e perfil na tabela users existente.
     * Deve rodar DEPOIS de create_families_table.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // FK para família — nullable porque o usuário existe antes de entrar numa família
            $table->foreignId('family_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('families')
                  ->nullOnDelete();

            // Perfil do usuário: pai ou filho
            $table->enum('role', ['parent', 'child'])->after('family_id')->default('child');

            // Campos opcionais de perfil
            $table->string('avatar')->nullable()->after('role');
            $table->date('birthdate')->nullable()->after('avatar');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['family_id']);
            $table->dropColumn(['family_id', 'role', 'avatar', 'birthdate']);
        });
    }
};
