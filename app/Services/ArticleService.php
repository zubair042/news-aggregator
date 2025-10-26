<?php

namespace App\Services;

use App\Models\Article;
use App\Models\UserPreference;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class ArticleService
{
    /**
     * Retrieve paginated list of articles based on filters and user preferences.
     *
     * @param array $filters
     * @param $user
     * @return LengthAwarePaginator
     */
    public function getArticles(array $filters, $user = null): LengthAwarePaginator
    {
        $query = Article::query();

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
                $sources = is_string($prefs->sources)
                    ? json_decode($prefs->sources, true)
                    : $prefs->sources;

                $query->whereIn('source', $sources);
            }

            if (!empty($prefs->categories)) {
                $categories = is_string($prefs->categories)
                    ? json_decode($prefs->categories, true)
                    : $prefs->categories;

                $query->whereIn('category', $categories);
            }

            if (!empty($prefs->authors)) {
                $authors = is_string($prefs->authors)
                    ? json_decode($prefs->authors, true)
                    : $prefs->authors;

                $query->whereIn('author', $authors);
            }
        }
    }


}
