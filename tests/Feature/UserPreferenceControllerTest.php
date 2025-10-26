<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use App\Models\UserPreference;
use App\Services\ArticleService;
use App\Services\UserPreferenceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserPreferenceControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, ['*']);
    }

    public function test_it_updates_preferences_successfully()
    {
        $payload = [
            'sources' => ['BBC', 'CNN'],
            'categories' => ['Tech', 'Sports'],
            'authors' => ['John Doe'],
        ];

        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->postJson('/api/preferences', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Preferences updated successfully.',
            ])
            ->assertJsonStructure([
                'data' => ['id', 'user_id', 'sources', 'categories', 'authors', 'created_at', 'updated_at']
            ]);

        $this->assertDatabaseHas('user_preferences', [
            'user_id' => $this->user->id,
        ]);
    }

    public function test_it_updates_existing_preferences_instead_of_creating_new()
    {
        $existing = UserPreference::factory()->create([
            'user_id' => $this->user->id,
            'sources' => ['OldSource'],
        ]);

        $payload = [
            'sources' => ['NewSource'],
            'categories' => ['UpdatedCategory'],
            'authors' => ['UpdatedAuthor'],
        ];

        $response = $this->postJson('/api/preferences', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Preferences updated successfully.',
            ]);

        $this->assertDatabaseHas('user_preferences', [
            'id' => $existing->id,
            'sources' => json_encode(['NewSource']),
        ]);
    }

    public function test_it_returns_validation_error_for_invalid_payload()
    {
        $payload = [
            'sources' => 'not-an-array', // invalid type
        ];

        $response = $this->postJson('/api/preferences', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['sources']);
    }

    public function test_it_handles_exceptions_gracefully()
    {
        Log::shouldReceive('error')->once();

        $this->mock(UserPreferenceService::class, function ($mock) {
            $mock->shouldReceive('updatePreferences')
                ->andThrow(new \Exception('Database connection lost'));
        });

        $payload = [
            'sources' => ['BBC'],
            'categories' => ['Tech'],
            'authors' => ['John Doe'],
        ];

        $response = $this->postJson('/api/preferences', $payload);

        $response->assertStatus(500)
            ->assertJson([
                'success' => false,
                'message' => 'Failed to update preferences.',
            ]);
    }

    /**
     * @return void
     */
    public function test_it_requires_authentication_to_update_preferences()
    {
        auth()->logout();

        $payload = [
            'sources' => ['BBC'],
        ];

        $response = $this->postJson('/api/preferences', $payload);

        $response->assertStatus(401);
    }

}
