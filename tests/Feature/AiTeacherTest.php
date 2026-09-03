<?php

namespace Tests\Feature;

use App\Models\AiConversation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiTeacherTest extends TestCase
{
    use RefreshDatabase;

    public function test_ai_teacher_page_redirects_to_home(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get(route('ai-teacher.index'))
            ->assertRedirect(route('home'));
    }

    public function test_user_can_send_message_to_ai_teacher(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('ai-teacher.send'), [
            'message' => '2x + 5 = 13',
            'mode' => 'explain',
            'level' => 'simple',
            'context' => [
                'problem' => '2x + 5 = 13',
                'source' => 'Тоглоом',
            ],
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'conversation' => ['id', 'title'],
            'message' => ['id', 'role', 'message'],
        ]);

        $this->assertDatabaseHas('ai_conversations', [
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('ai_messages', [
            'role' => 'user',
            'message' => '2x + 5 = 13',
        ]);

        $this->assertDatabaseHas('ai_messages', [
            'role' => 'assistant',
        ]);
    }

    public function test_ai_helper_button_is_visible_outside_tests(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get(route('toys.index'))
            ->assertOk()
            ->assertSee('Тайлбар авах');
    }

    public function test_ai_helper_is_hidden_on_test_page(): void
    {
        $user = User::factory()->create();
        $this->seed(\Database\Seeders\CurriculumSeeder::class);
        $this->actingAs($user);

        $topic = \App\Models\Topic::find(1);

        $this->post(route('test.topic.take', $topic->slug), [
            'student_name' => 'Test User',
        ])
            ->assertOk()
            ->assertDontSee('Тайлбар авах');
    }

    public function test_user_cannot_view_another_users_conversation(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $conversation = AiConversation::create([
            'user_id' => $owner->id,
            'title' => 'Нууц ярилцлага',
            'topic' => 'explain',
        ]);

        $this->actingAs($other);

        $this->getJson(route('ai-teacher.show', $conversation))
            ->assertNotFound();
    }
}
