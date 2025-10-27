<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\ArticleFilterRequest;
use App\Http\Resources\ArticleResource;
use App\Services\ArticleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    protected ArticleService $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    /**
     * Fetch a paginated list of articles based on filters and user preferences.
     *
     * @param ArticleFilterRequest $request
     * @return JsonResponse
     */
    public function index(ArticleFilterRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $articles = $this->articleService->getArticles($request->validated(), $user);
            $meta = [
                'current_page'  => $articles->currentPage(),
                'last_page'     => $articles->lastPage(),
                'per_page'      => $articles->perPage(),
                'total'         => $articles->total(),
            ];

            return ResponseHelper::apiResponse(
                true,
                'Articles fetched successfully.',
                [
                    'articles' => ArticleResource::collection($articles),
                    'meta' => $meta
                ]
            );

        } catch (\Throwable $e) {
            return ResponseHelper::apiResponse(false, 'Failed to fetch articles.', null, 500,
                ['error' => $e->getMessage()]
            );
        }
    }

    /**
     * Get article detail
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $article = $this->articleService->getArticleById($id);

            if (!$article) {
                return ResponseHelper::apiResponse(
                    false,
                    'Article not found.',
                    null,
                    404
                );
            }

            return ResponseHelper::apiResponse(
                true,
                'Article retrieved successfully.',
                new ArticleResource($article)
            );
        } catch (\Throwable $e) {
            return ResponseHelper::apiResponse(
                false,
                'Failed to retrieve article.',
                null,
                500,
                $e->getMessage()
            );
        }
    }

}
