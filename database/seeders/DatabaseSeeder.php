<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            RoleSeeder::class,
        ]);

        \App\Models\User::factory(10)->create();
        \App\Models\LessonType::factory(5)->create();
        \App\Models\Subscription::factory(5)->create();
        \App\Models\Lesson::factory(20)->create();
        \App\Models\TrainerDetail::factory(5)->create();
        \App\Models\Appointment::factory(10)->create();
        \App\Models\Testimonial::factory(10)->create();
    }
}
