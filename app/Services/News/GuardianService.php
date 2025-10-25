<?php

namespace App\Services\News;

use Illuminate\Support\Facades\Http;

class GuardianService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = 'https://content.guardianapis.com';
        $this->apiKey = config('services.guardian.key');
    }

    public function fetchArticles(): array
    {
        $response = Http::get("{$this->baseUrl}/search", [
            'api-key' => $this->apiKey,
            'show-fields' => 'headline,trailText,byline,short-url',
            'page-size' => 10,
        ]);

        if ($response->failed()) {
            return [];
        }

        return collect($response->json('response.results', []))
            ->map(fn($item) => [
                'title' => $item['webTitle'] ?? '',
                'description' => $item['fields']['trailText'] ?? '',
                'author' => $item['fields']['byline'] ?? null,
                'category' => $item['sectionName'] ?? null,
                'source' => 'The Guardian',
                'url' => $item['webUrl'] ?? '',
                'published_at' => $item['webPublicationDate'] ?? now(),
            ])
            ->toArray();
    }
}
