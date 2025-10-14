<?php

namespace Tests\Feature;

use App\Models\LessonType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonTypeApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_lesson_types()
    {
        LessonType::factory()->count(3)->create();

        $response = $this->getJson('/api/lesson-types');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    public function test_can_show_lesson_type()
    {
        $lessonType = LessonType::factory()->create();

        $response = $this->getJson("/api/lesson-types/{$lessonType->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $lessonType->id,
                     'name' => $lessonType->name,
                 ]);
    }

    public function test_can_create_lesson_type()
    {
        $data = [
            'name' => 'Yoga',
        ];

        $response = $this->postJson('/api/lesson-types', $data);

        $response->assertStatus(201)
                 ->assertJson($data);
    }

    public function test_can_update_lesson_type()
    {
        $lessonType = LessonType::factory()->create();

        $data = [
            'name' => 'Updated Yoga',
        ];

        $response = $this->putJson("/api/lesson-types/{$lessonType->id}", $data);

        $response->assertStatus(200)
                 ->assertJson($data);
    }

    public function test_can_delete_lesson_type()
    {
        $lessonType = LessonType::factory()->create();

        $response = $this->deleteJson("/api/lesson-types/{$lessonType->id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Lesson type deleted.']);
    }
}