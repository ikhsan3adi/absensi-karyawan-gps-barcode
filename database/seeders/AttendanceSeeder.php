<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $start = Carbon::now()->subDays(60);
        $end = Carbon::now()->addDay();
        $dates = $start->range($end)->toArray();

        $statuses = ['present', 'present', 'present', 'present', 'late', 'excused', 'sick'];

        foreach ($dates as $date) {
            if ($date->isWeekend()) {
                continue;
            }

            /** @var User[] */
            $users = User::inRandomOrder()->where('group', 'user')->get();

            foreach ($users as $user) {
                $status = fake()->randomElement($statuses);
                $attr = ['date' => $date->toDateString(), 'user_id' => $user->id];
                switch ($status) {
                    case 'present':
                        Attendance::factory()->present()->create($attr);
                        break;
                    case 'late':
                        Attendance::factory()->present(late: true)->create($attr);
                        break;
                    case 'excused':
                        Attendance::factory()->excused()->create($attr);
                        break;
                    case 'sick':
                        Attendance::factory()->excused(sick: true)->create($attr);
                        break;
                    default:
                        Attendance::factory()->absent()->create($attr);
                        break;
                }
            }
        }
    }
}
