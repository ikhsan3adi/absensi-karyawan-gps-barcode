<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

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
            'name' => fake()->unique()->randomElement(['Barcode 1', 'Barcode 2', 'Barcode 3', 'Barcode 4', 'Barcode 5']),
            'value' => fake()->ean13(),
            'radius' => 10,
            'coordinates' => $this->generateCoordinates(),
            'time_limit' => '09:00:00',
            // 'time_in_valid_from' => '05:00:00',
            // 'time_in_valid_until' => '09:00:00',
            // 'time_out_valid_from' => '15:00:00',
            // 'time_out_valid_until' => '20:00:00',
        ];
    }

    function generateCoordinates()
    {
        $lat = fake()->latitude(-90, 90);
        $lng = fake()->longitude(-90, 90);
        $sql = "ST_GeomFromText('POINT(" . $lat . ' ' . $lng . ")', 4326)";
        return DB::raw($sql);
    }
}
