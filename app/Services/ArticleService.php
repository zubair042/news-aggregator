<?php

namespace App\Services;

use App\Models\Article;
use App\Models\UserPreference;
use Illuminate\Database\Eloquent\Builder;

class ArticleService
{
    public function getArticles(array $filters, $user = null)
    {
        $query = Article::query();

        // Search filter
        if (!empty($filters['search'])) {
            $query->where(function (Builder $q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Filter by category, source, author
        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }
        if (!empty($filters['source'])) {
            $query->where('source', $filters['source']);
        }
        if (!empty($filters['author'])) {
            $query->where('author', $filters['author']);
        }

        // Date filter
        if (!empty($filters['from_date'])) {
            $query->whereDate('published_at', '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $query->whereDate('published_at', '<=', $filters['to_date']);
        }

        // Apply user preferences if available
        if ($user) {
            $prefs = UserPreference::where('user_id', $user->id)->first();

            if ($prefs) {
                if (!empty($prefs->sources)) {
                    $sources = json_decode($prefs->sources, true);
                    $query->whereIn('source', $sources);
                }

                if (!empty($prefs->categories)) {
                    $categories = json_decode($prefs->categories, true);
                    $query->whereIn('category', $categories);
                }

                if (!empty($prefs->authors)) {
                    $authors = json_decode($prefs->authors, true);
                    $query->whereIn('author', $authors);
                }
            }
        }

        // Return paginated results
        return $query->latest('published_at')->paginate(10);
    }
}
