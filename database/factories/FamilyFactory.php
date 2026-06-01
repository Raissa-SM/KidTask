<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Family>
 */
class FamilyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'        => 'Família ' . fake()->lastName(),
            'invite_code' => strtoupper(Str::random(6)),
        ];
    }
}
