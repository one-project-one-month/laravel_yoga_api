<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Role;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement(['admin', 'trainer', 'student']),
            'description' => $this->faker->sentence(),
        ];
    }
}
