<?php

namespace Database\Factories;

use App\Models\Division;
use App\Models\Education;
use App\Models\JobTitle;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Jetstream\Features;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gender = fake()->randomElement(['male', 'female']);
        return [
            'nip' => fake()->numerify('#################'),
            'name' => fake()->name($gender),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'phone' => fake()->phoneNumber(),
            'gender' => $gender,
            'birth_date' => fake()->date(),
            'birth_place' => fake()->city(),
            'address' => fake()->address(),
            'city' => fake()->city(),
            'group' => 'user',
            'password' => static::$password ??= Hash::make('password'),
            'raw_password' => 'password',
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'remember_token' => Str::random(10),
            'profile_photo_path' => null,
            'education_id' => Education::inRandomOrder()->first()?->id,
            'division_id' => Division::inRandomOrder()->first()?->id,
            'job_title_id' => JobTitle::inRandomOrder()->first()?->id,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user should be admin.
     */
    public function admin(bool $superadmin = false): static
    {
        return $this->state(fn (array $attributes) => [
            'nip' => '0000000000000000',
            'phone' => '00000000000',
            'birth_date' => null,
            'birth_place' => null,
            'address' => '',
            'city' => '',
            'group' => $superadmin ? 'superadmin' : 'admin',
            'gender' => 'male',
        ]);
    }

    /**
     * ! NOT USED
     * Indicate that the user should have a personal team.
     */
    public function withPersonalTeam(callable $callback = null): static
    {
        if (!Features::hasTeamFeatures()) {
            return $this->state([]);
        }

        return $this->has(
            Team::factory()
                ->state(fn (array $attributes, User $user) => [
                    'name' => $user->name . '\'s Team',
                    'user_id' => $user->id,
                    'personal_team' => true,
                ])
                ->when(is_callable($callback), $callback),
            'ownedTeams'
        );
    }
}
