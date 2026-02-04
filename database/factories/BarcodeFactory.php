<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Barcode>
 */
class BarcodeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()
                ->randomElement(['Barcode 1', 'Barcode 2', 'Barcode 3', 'Barcode 4', 'Barcode 5']),
            'value' => fake()->ean13(),
            'radius' => 50,
            'latitude' => fake()->latitude(-6.92941, -6.89618),
            'longitude' => fake()->longitude(107.56688, 107.66250),
        ];
    }
}
