<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Lesson;
use App\Models\LessonType;
use App\Models\User;

class LessonFactory extends Factory
{
    protected $model = Lesson::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence(3),
            'slug' => $this->faker->unique()->slug(),
            'description' => $this->faker->paragraph(),
            'level' => $this->faker->randomElement(['beginner', 'intermediate', 'advanced']),
            'video_url' => $this->faker->url(),
            'lesson_type_id' => LessonType::inRandomOrder()->first()->id ?? LessonType::factory(),
            'duration_minutes' => $this->faker->numberBetween(10, 120),
            'is_free' => $this->faker->boolean(30),
            'is_premium' => $this->faker->boolean(50),
            'trainer_id' => User::inRandomOrder()->first()->id ?? User::factory(),
        ];
    }
}
