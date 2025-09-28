<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Appointment;
use App\Models\User;

class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition()
    {
        $student = User::inRandomOrder()->first() ?? User::factory();
        $trainer = User::inRandomOrder()->first() ?? User::factory();
        $admin = User::inRandomOrder()->first() ?? User::factory();

        return [
            'user_id' => $student->id,
            'trainer_id' => $trainer->id,
            'admin_id' => $admin->id,
            'appointment_date' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'appointment_fees' => $this->faker->randomFloat(2, 10, 100),
            'meet_link' => $this->faker->url(),
            'is_approved' => $this->faker->boolean(),
            'is_completed' => $this->faker->boolean(),
        ];
    }
}
