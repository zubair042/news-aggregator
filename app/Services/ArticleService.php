<?php

namespace App\Services;

use App\Models\Article;
use App\Models\UserPreference;
use App\Repositories\ArticleRepository;
use App\Services\News\GuardianService;
use App\Services\News\NewsApiService;
use App\Services\News\NYTimesService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class ArticleService
{
    protected ArticleRepository $articleRepository;
    protected NewsApiService $newsApi;
    protected GuardianService $guardian;
    protected NYTimesService $nytimes;

    public function __construct(
        ArticleRepository $articleRepository,
        NewsApiService    $newsApi,
        GuardianService   $guardian,
        NYTimesService    $nytimes
    )
    {
        $this->articleRepository = $articleRepository;
        $this->newsApi = $newsApi;
        $this->guardian = $guardian;
        $this->nytimes = $nytimes;
    }

    /**
     * Retrieve paginated list of articles based on filters and user preferences.
     *
     * @param array $filters
     * @param $user
     * @return LengthAwarePaginator
     */
    public function getArticles(array $filters, $user = null): LengthAwarePaginator
    {
        return $this->articleRepository->getArticles($filters, $user);
    }

    /**
     * Fetches articles from all integrated news sources and stores them in the database
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
            $this->articleRepository->createIfNotExists($data);
        }
    }

    /**
     * Get Article record by ID
     *
     * @param int $id
     * @return Article|null
     */
    public function getArticleById(int $id): ?Article
    {
        return $this->articleRepository->getArticleById($id);
    }
}
