<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::insert([
            ['name' => 'admin', 'description' => 'System administrator'],
            ['name' => 'trainer', 'description' => 'Yoga trainer'],
            ['name' => 'student', 'description' => 'Regular user'],
        ]);
    }
}
