<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use App\Models\UserPreference;
use App\Services\ArticleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ArticleControllerTest extends TestCase
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

    /**
     * @return void
     */
    public function test_fetches_articles_successfully(): void
    {
        Article::factory()->count(5)->create();

        $response = $this->getJson('/api/articles');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Articles fetched successfully.',
            ])
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'source', 'category', 'author', 'published_at']
                ],
                'meta' => ['current_page', 'last_page', 'per_page', 'total']
            ]);
    }

    /**
     * @return void
     */
    public function test_filters_articles_by_search_keyword(): void
    {
        Article::factory()->create(['title' => 'Laravel 11 Released']);
        Article::factory()->create(['title' => 'React JS Updates']);

        $response = $this->getJson('/api/articles?search=Laravel');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertStringContainsString('Laravel', $data[0]['title']);
    }

    /**
     * @return void
     */
    public function test_filters_articles_by_category_source_and_author(): void
    {
        Article::factory()->create(['category' => 'Tech', 'source' => 'BBC', 'author' => 'John']);
        Article::factory()->create(['category' => 'Sports', 'source' => 'CNN', 'author' => 'Jane']);

        $response = $this->getJson('/api/articles?category=Tech&source=BBC&author=John');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    /**
     * @return void
     */
    public function test_filters_articles_by_date_range(): void
    {
        Article::factory()->create(['published_at' => now()->subDays(3)]);
        Article::factory()->create(['published_at' => now()->subDays(10)]);

        $response = $this->getJson('/api/articles?from_date=' . now()->subDays(5)->toDateString());

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    /**
     * @return void
     */
    public function test_applies_user_preferences_for_category_source_and_author(): void
    {
        UserPreference::factory()->create([
            'user_id' => $this->user->id,
            'sources' => ['BBC'],
            'categories' => ['Tech'],
            'authors' => ['John Doe'],
        ]);

        Article::factory()->create(['source' => 'BBC', 'category' => 'Tech', 'author' => 'John Doe']);
        Article::factory()->create(['source' => 'CNN', 'category' => 'Sports', 'author' => 'Jane']);

        $response = $this->getJson('/api/articles');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('BBC', $data[0]['source']);
    }

    /**
     * @return void
     */
    public function test_returns_empty_data_if_no_articles_found(): void
    {
        $response = $this->getJson('/api/articles?category=Unknown');
        $response->assertStatus(200);
        $this->assertEmpty($response->json('data'));
    }

    /**
     * @return void
     */
    public function test_handles_exceptions_gracefully(): void
    {
        Log::shouldReceive('error')->once();

        $this->mock(ArticleService::class, function ($mock) {
            $mock->shouldReceive('getArticles')->andThrow(new \Exception('Database error'));
        });

        $response = $this->getJson('/api/articles');

        $response->assertStatus(500)
            ->assertJson([
                'success' => false,
                'message' => 'Failed to fetch articles',
            ]);
    }
}
