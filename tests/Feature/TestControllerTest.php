<?php

namespace Tests\Feature;

use App\Models\Topic;
use App\Models\User;
use Database\Seeders\CurriculumSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_topic_quiz_uses_one_question_per_attempt(): void
    {
        $user = User::factory()->create();
        $this->seed(CurriculumSeeder::class);
        $this->actingAs($user);

        $topic = Topic::find(1);

        $response = $this->post(route('test.topic.take', $topic->slug), [
            'student_name' => 'Test User',
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('tests.take');
        $response->assertViewHas('questions', function ($questions) {
            return $questions->count() === 1;
        });
    }

    public function test_billing_page_shows_math_challenge_section(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('billing.index'));

        $response->assertStatus(200);
        $response->assertSee('Өдөр тутмын сорил');
        $response->assertSee('Game Zone');
        $response->assertSee('Төлбөрийн түүх');
    }

    public function test_home_page_shows_premium_teaser_section(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('Premium эрх');
        $response->assertSee('Бүх сэдэв');
    }
}
