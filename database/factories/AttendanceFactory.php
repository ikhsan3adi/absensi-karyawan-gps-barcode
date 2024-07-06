<?php

namespace Database\Factories;

use App\Models\Barcode;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = $this->faker->randomElement(['present', 'late', 'absent', 'excused', 'sick']);
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'date' => $this->faker->date(),
            'status' => $status,
            'note' => $status == 'sick' || $status == 'excused' ? $this->faker->sentence() : null,
        ];
    }

    public function absent(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'absent',
            ];
        });
    }

    public function present(bool $late = false): static
    {
        return $this->state(function (array $attributes) use ($late) {
            /** @var Barcode */
            $barcode = Barcode::inRandomOrder()->first();
            /** @var Shift */
            $shift = Shift::inRandomOrder()->first();
            $time_in = Carbon::parse($shift->start_time)->subMinutes(rand(0, max: 15))->toTimeString();
            $time_out = Carbon::parse($shift->end_time)->addMinutes(rand(0, max: 15))->toTimeString();
            if ($late) {
                $time_in = Carbon::parse($shift->start_time)->addMinutes(rand(min: 1, max: 15))->toTimeString();
            }
            return [
                'barcode_id' => $barcode->id,
                'time_in' => $time_in,
                'time_out' => $time_out,
                'status' => $late ? 'late' : 'present',
                'shift_id' => $shift->id,
                'latitude' => $barcode->latitude,
                'longitude' => $barcode->longitude,
                'note' => null,
            ];
        });
    }

    public function excused(bool $sick = false): static
    {
        return $this->state(function (array $attributes) use ($sick) {
            return [
                'status' => $sick ? 'sick' : 'excused',
                'note' => $this->faker->sentence(),
                'attachment' => $this->faker->imageUrl(),
            ];
        });
    }
}
