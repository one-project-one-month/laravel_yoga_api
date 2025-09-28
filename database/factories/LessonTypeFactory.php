<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\LessonType;

class LessonTypeFactory extends Factory
{
    protected $model = LessonType::class;

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word(),
            'description' => $this->faker->paragraph(),
        ];
    }
}
