<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class FakeDataSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        (new DatabaseSeeder)->run();
        User::factory(10)->create();
        (new AttendanceSeeder)->run();
    }
}
