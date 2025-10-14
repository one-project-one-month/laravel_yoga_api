<?php

namespace Database\Factories;

use App\Models\Lesson;
use Illuminate\Database\Eloquent\Factories\Factory;

class LessonFactory extends Factory
{
    protected $model = Lesson::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
            'duration_minutes' => $this->faker->numberBetween(1, 120),
            'order' => $this->faker->numberBetween(1, 100),
        ];
    }
}