<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            RoleSeeder::class,
            AppointmentSeeder::class,
        ]);

        \App\Models\User::create([
            'full_name' => "John Doe",
            'email' => "johndoe@gmail.com",
            'password' => Hash::make('John123456'),
            'role_id' => 1
        ]);

        \App\Models\User::create([
            'full_name' => "John Doe",
            'email' => "johndoe@gmail.com",
            'password' => Hash::make('John123456'),
            'role_id' => 1
        ]);

        \App\Models\User::factory(10)->create();
        \App\Models\LessonType::factory(5)->create();
        \App\Models\Subscription::factory(5)->create();
        \App\Models\Lesson::factory(20)->create();
        \App\Models\TrainerDetail::factory(5)->create();
        
        \App\Models\Testimonial::factory(10)->create();
    }
}
