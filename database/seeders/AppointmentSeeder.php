<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Appointment;
use App\Models\User;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure some users exist
        $users = User::factory(10)->create();

        // Use existing users for foreign keys
        Appointment::factory(20)->create([
            'user_id' => $users->random()->id,
            'admin_id' => $users->random()->id,
            'trainer_id' => $users->random()->id,
        ]);
    }
}
