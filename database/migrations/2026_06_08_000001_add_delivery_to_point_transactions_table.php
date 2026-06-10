<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('point_transactions', function (Blueprint $table) {
            $table->foreignId('reward_id')
                ->nullable()
                ->after('task_completion_id')
                ->constrained()
                ->nullOnDelete();

            $table->timestamp('delivered_at')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('point_transactions', function (Blueprint $table) {
            $table->dropForeign(['reward_id']);
            $table->dropColumn(['reward_id', 'delivered_at']);
        });
    }
};
