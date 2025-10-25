<?php

namespace App\Services\News;

use Illuminate\Support\Facades\Http;

class NYTimesService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = 'https://api.nytimes.com/svc/topstories/v2';
        $this->apiKey = config('services.nytimes.key');
    }

    public function fetchArticles(): array
    {
        $response = Http::get("{$this->baseUrl}/world.json", [
            'api-key' => $this->apiKey,
        ]);

        if ($response->failed()) {
            return [];
        }

        return collect($response->json('results', []))
            ->map(fn($article) => [
                'title' => $article['title'] ?? '',
                'description' => $article['abstract'] ?? '',
                'author' => $article['byline'] ?? null,
                'category' => $article['section'] ?? null,
                'source' => 'New York Times',
                'url' => $article['url'] ?? '',
                'published_at' => $article['published_date'] ?? now(),
            ])
            ->toArray();
    }
}
