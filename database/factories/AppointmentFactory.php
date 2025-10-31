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

        return [
            'user_id' => $student->id,
            'appointment_date' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'appointment_time' => $this->faker->time('H:i'),
            'appointment_type' => $this->faker->paragraph(2),
            'appointment_fees' => $this->faker->randomFloat(2, 10, 100),
            'meet_link' => $this->faker->url(),
            'is_approved' => $this->faker->randomElement(["pending", "accept", "reject"]),
            'is_completed' => $this->faker->boolean(),
        ];
    }
}
