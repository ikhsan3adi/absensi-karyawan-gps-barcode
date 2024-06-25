<?php

namespace Database\Seeders;

use App\Models\Barcode;
use App\Models\Division;
use App\Models\Education;
use App\Models\JobTitle;
use App\Models\Shift;
use App\Models\User;
use Database\Factories\DivisionFactory;
use Database\Factories\EducationFactory;
use Database\Factories\JobTitleFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        (new AdminSeeder)->run();
        foreach (DivisionFactory::$divisions as $value) {
            if (Division::where('name', $value)->exists()) {
                continue;
            }
            Division::create(['name' => $value]);
        }
        foreach (EducationFactory::$educations as $value) {
            if (Education::where('name', $value)->exists()) {
                continue;
            }
            Education::create(['name' => $value]);
        }
        foreach (JobTitleFactory::$jobTitles as $value) {
            if (JobTitle::where('name', $value)->exists()) {
                continue;
            }
            JobTitle::create(['name' => $value]);
        }
        Barcode::factory(1)->create(['name' => 'Barcode 1']);
        Shift::factory(2)->create();
    }
}
