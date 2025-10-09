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
        $universities = [
            'University of Yangon',
            'Yangon University of Economics',
            'Technological University',
            'Dagon University',
            'East Yangon university',
            'West Yangon university',
        ];

        return [
            'trainer_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'bio' => $this->faker->paragraph(),
            'university_name' => $this->faker->randomElement($universities),
            'degree' => $this->faker->paragraph(),
            'city' => $this->faker->city(),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
            'salary' => $this->faker->randomFloat(2, 500, 5000),
            'branch_location' => $this->faker->city(),
        ];
    }
}
