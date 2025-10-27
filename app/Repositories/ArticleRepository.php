<?php

namespace App\Repositories;

use App\Models\Article;
use App\Models\UserPreference;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ArticleRepository
{
    private Article $article;

    public function __construct()
    {
        $this->article = new Article();
    }

    /**
     * Fetch articles with optional query params
     *
     * @param array $filters
     * @param $user
     * @return LengthAwarePaginator
     */
    public function getArticles(array $filters, $user = null): LengthAwarePaginator
    {
        $query = $this->article->query();

        $this->applySearchFilters($query, $filters);
        $this->applyCategorySourceAuthorFilters($query, $filters);
        $this->applyDateFilters($query, $filters);

        if ($user) {
            $this->applyUserPreferences($query, $user);
        }

        return $query->latest('published_at')->paginate(10);
    }

    /**
     * Apply search filters for title and description.
     *
     * @param Builder $query
     * @param array $filters
     * @return void
     */
    private function applySearchFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['search'])) {
            $query->where(function (Builder $q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }
    }

    /**
     * Apply category, source, and author filters.
     *
     * @param Builder $query
     * @param array $filters
     * @return void
     */
    private function applyCategorySourceAuthorFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (!empty($filters['source'])) {
            $query->where('source', $filters['source']);
        }

        if (!empty($filters['author'])) {
            $query->where('author', $filters['author']);
        }
    }

    /**
     * Apply date range filters for published articles.
     *
     * @param Builder $query
     * @param array $filters
     * @return void
     */
    private function applyDateFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['from_date'])) {
            $query->whereDate('published_at', '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $query->whereDate('published_at', '<=', $filters['to_date']);
        }
    }

    /**
     * Apply filters based on the user's saved preferences.
     *
     * @param Builder $query
     * @param $user
     * @return void
     */
    private function applyUserPreferences(Builder $query, $user): void
    {
        $prefs = UserPreference::where('user_id', $user->id)->first();

        if ($prefs) {
            if (!empty($prefs->sources)) {
                $query->whereIn('source', $prefs->sources);
            }

            if (!empty($prefs->categories)) {
                $query->whereIn('category', $prefs->categories);
            }

            if (!empty($prefs->authors)) {
                $query->whereIn('author', $prefs->authors);
            }
        }
    }

    /**
     * Create a new article if it doesn't already exist.
     *
     * @param array $data
     * @return Article|null
     */
    public function createIfNotExists(array $data): ?Article
    {
        $existing = $this->article->where('url', $data['url'])->first();

        if ($existing) {
            return null; // skip duplicates
        }

        return $this->article->create($data);
    }

    /**
     * Get Article by ID
     *
     * @param int $id
     * @return Article|null
     */
    public function getArticleById(int $id): ?Article
    {
        return $this->article->find($id);
    }
}
