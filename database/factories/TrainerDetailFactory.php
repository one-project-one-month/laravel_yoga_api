<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\TrainerDetail;
use App\Models\User;

class TrainerDetailFactory extends Factory
{
    protected $model = TrainerDetail::class;

    public function definition()
    {
        return [
            'trainer_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'bio' => $this->faker->paragraph(),
            'description' => $this->faker->paragraph(),
            'salary' => $this->faker->randomFloat(2, 500, 5000),
            'branch_location' => $this->faker->city(),
        ];
    }
}
