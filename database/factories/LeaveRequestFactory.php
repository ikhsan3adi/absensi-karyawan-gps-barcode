<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeaveRequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => fake()->randomElement(['excused', 'sick']),
            'from_date' => fake()->date(),
            'to_date' => fake()->date(),
            'note' => fake()->sentence(),
            'status' => 'pending',
        ];
    }
}
