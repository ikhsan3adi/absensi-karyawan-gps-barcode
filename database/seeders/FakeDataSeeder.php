<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FakeDataSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        (new DatabaseSeeder)->run();
        User::factory(10)->create();
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'user@example.com',
        ]);
        (new AttendanceSeeder)->run();
    }
}
