<?php

namespace App\Services;

use App\Models\Article;
use App\Services\News\NewsApiService;
use App\Services\News\GuardianService;
use App\Services\News\NYTimesService;

class NewsAggregatorService
{
    protected NewsApiService $newsApi;
    protected GuardianService $guardian;
    protected NYTimesService $nytimes;

    public function __construct(
        NewsApiService $newsApi,
        GuardianService $guardian,
        NYTimesService $nytimes
    ) {
        $this->newsApi = $newsApi;
        $this->guardian = $guardian;
        $this->nytimes = $nytimes;
    }

    /**
     * Fetches articles from all integrated news sources and stores them in the database, updating existing records to prevent duplicates.
     *
     * @return void
     */
    public function fetchAndStore(): void
    {
        $articles = collect()
            ->merge($this->newsApi->fetchArticles())
            ->merge($this->guardian->fetchArticles())
            ->merge($this->nytimes->fetchArticles());

        foreach ($articles as $data) {
            Article::updateOrCreate(
                ['url' => $data['url']], // avoid duplicates
                $data
            );
        }
    }
}
