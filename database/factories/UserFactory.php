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
            'full_name' => $this->faker->name(),
            'nick_name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'role_id' => Role::inRandomOrder()->first()->id ?? Role::factory(),
            'profile_url' => $this->faker->imageUrl(200, 200),
            'ph_no_telegram' => $this->faker->phoneNumber(),
            'ph_no_whatsapp' => $this->faker->phoneNumber(),
            'date_of_birth' => $this->faker->date(),
            'place_of_birth' => $this->faker->city(),
            'address' => $this->faker->address(),
            'weight' => $this->faker->randomFloat(1, 40, 120),
            'daily_routine_for_weekly' => $this->faker->paragraph(),
            'special_request' => $this->faker->bloodType(),
            'is_verified' => $this->faker->boolean(80),
            'is_premium' => $this->faker->boolean(50),
            'is_first_time_appointment' => $this->faker->boolean(50),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
        ];
    }
}
