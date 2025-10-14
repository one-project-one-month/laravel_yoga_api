<?php

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\Trainer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonTrainerTest extends TestCase
{
    use RefreshDatabase;

    public function test_assign_lesson_to_trainer()
    {
        $lesson = Lesson::factory()->create();
        $trainer = Trainer::factory()->create();

        $response = $this->postJson('/api/lesson-trainer', [
            'lesson_id' => $lesson->id,
            'trainer_id' => $trainer->id,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('lesson_trainer', [
            'lesson_id' => $lesson->id,
            'trainer_id' => $trainer->id,
        ]);
    }

    public function test_assign_lesson_to_trainer_validation()
    {
        $response = $this->postJson('/api/lesson-trainer', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['lesson_id', 'trainer_id']);
    }

    public function test_assign_nonexistent_lesson_to_trainer()
    {
        $trainer = Trainer::factory()->create();

        $response = $this->postJson('/api/lesson-trainer', [
            'lesson_id' => 999,
            'trainer_id' => $trainer->id,
        ]);

        $response->assertStatus(404);
    }

    public function test_assign_lesson_to_nonexistent_trainer()
    {
        $lesson = Lesson::factory()->create();

        $response = $this->postJson('/api/lesson-trainer', [
            'lesson_id' => $lesson->id,
            'trainer_id' => 999,
        ]);

        $response->assertStatus(404);
    }
}