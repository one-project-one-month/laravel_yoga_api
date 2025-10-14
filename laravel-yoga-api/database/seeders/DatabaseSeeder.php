<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lesson;
use App\Models\LessonType;
use App\Models\Trainer;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Seed lesson types
        LessonType::factory()->count(10)->create();

        // Seed trainers
        Trainer::factory()->count(5)->create();

        // Seed lessons
        Lesson::factory()->count(20)->create();
    }
}