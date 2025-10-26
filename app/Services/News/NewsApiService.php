<?php

namespace App\Services\News;

use Illuminate\Support\Facades\Http;

class NewsApiService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = 'https://newsapi.org/v2';
        $this->apiKey = config('services.newsapi.key');
    }

    /**
     * Fetches and formats headlines from NewsAPI
     *
     * @return array
     */
    public function fetchArticles(): array
    {
        $response = Http::get("{$this->baseUrl}/top-headlines", [
            'country' => 'us',
            'apiKey' => $this->apiKey,
        ]);

        if ($response->failed()) {
            return [];
        }

        return collect($response->json('articles', []))
            ->map(fn($article) => [
                'title' => $article['title'] ?? '',
                'description' => $article['description'] ?? '',
                'author' => $article['author'] ?? null,
                'category' => $article['source']['name'] ?? null,
                'source' => 'NewsAPI',
                'url' => $article['url'] ?? '',
                'published_at' => $article['publishedAt'] ?? now(),
            ])
            ->toArray();
    }
}
