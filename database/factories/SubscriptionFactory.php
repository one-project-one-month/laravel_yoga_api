<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Subscription;
use App\Models\LessonType;

class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 10, 200),
            'lesson_type_id' => LessonType::inRandomOrder()->first()->id ?? LessonType::factory(),
            'duration' => $this->faker->randomElement(['1 month', '3 months', '6 months']),
        ];
    }
}
