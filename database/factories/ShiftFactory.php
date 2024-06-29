<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shift>
 */
class ShiftFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $i = fake()->unique()->randomElement([0, 1]);
        return [
            'name' => ['Shift 1', 'Shift 2'][$i],
            'start_time' => ['08:00', '19:00'][$i],
            'end_time' => ['17:00', '02:00'][$i],
        ];
    }
}
