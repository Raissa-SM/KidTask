<?php

namespace Database\Factories;

use App\Models\Family;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    public function definition(): array
    {
        $recurrence = fake()->randomElement(['none', 'daily', 'weekly', 'monthly']);

        return [
            'family_id'      => Family::factory(),
            'created_by'     => User::factory(),
            'title'          => fake()->randomElement([
                'Lavar a louça',
                'Arrumar a cama',
                'Varrer o quarto',
                'Tirar o lixo',
                'Estudar matemática',
                'Regar as plantas',
            ]),
            'description'    => fake()->optional()->sentence(),
            'points'         => fake()->numberBetween(1, 20),
            'recurrence'     => $recurrence,
            'recurrence_day' => $recurrence === 'weekly'
                ? fake()->numberBetween(0, 6)
                : ($recurrence === 'monthly' ? fake()->numberBetween(1, 28) : null),
            'due_date'       => $recurrence === 'none'
                ? fake()->dateTimeBetween('now', '+30 days')
                : null,
            'reminder_time'  => fake()->optional()->time('H:i'),
            'is_active'      => true,
        ];
    }
}
