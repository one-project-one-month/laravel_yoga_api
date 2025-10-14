<?php

namespace Tests\Feature;

use App\Models\Lesson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_lessons()
    {
        Lesson::factory()->count(5)->create();

        $response = $this->getJson('/api/lessons');

        $response->assertStatus(200)
                 ->assertJsonCount(5, 'data');
    }

    public function test_can_show_lesson_details()
    {
        $lesson = Lesson::factory()->create();

        $response = $this->getJson("/api/lessons/{$lesson->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $lesson->id,
                     'title' => $lesson->title,
                 ]);
    }

    public function test_can_create_new_lesson()
    {
        $data = [
            'title' => 'New Lesson',
            'content' => 'Lesson content',
            'duration_minutes' => 60,
            'order' => 1,
        ];

        $response = $this->postJson('/api/lessons', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'title' => 'New Lesson',
                 ]);

        $this->assertDatabaseHas('lessons', $data);
    }

    public function test_can_update_lesson()
    {
        $lesson = Lesson::factory()->create();

        $data = [
            'title' => 'Updated Lesson',
        ];

        $response = $this->putJson("/api/lessons/{$lesson->id}", $data);

        $response->assertStatus(200)
                 ->assertJson([
                     'title' => 'Updated Lesson',
                 ]);

        $this->assertDatabaseHas('lessons', $data);
    }

    public function test_can_delete_lesson()
    {
        $lesson = Lesson::factory()->create();

        $response = $this->deleteJson("/api/lessons/{$lesson->id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Lesson deleted.']);

        $this->assertDeleted($lesson);
    }
}