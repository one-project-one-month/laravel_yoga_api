<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\Role;

class UserFactory extends Factory
{
    protected $model = User::class;
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'role_id' => Role::inRandomOrder()->first()->id ?? Role::factory(),
            'avatar' => $this->faker->imageUrl(200, 200),
            'ph_no' => $this->faker->phoneNumber(),
            'date_of_birth' => $this->faker->date(),
            'is_verified' => $this->faker->boolean(80),
            'is_premium' => $this->faker->boolean(50),
            'is_first_time_appointment' => $this->faker->boolean(50),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
        ];
    }

}
