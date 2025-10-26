<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\ArticleFilterRequest;
use App\Http\Resources\ArticleResource;
use App\Services\ArticleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

            return response()->json([
                'success' => true,
                'message' => 'Articles fetched successfully.',
                'data' => ArticleResource::collection($articles),
                'meta' => [
                    'current_page' => $articles->currentPage(),
                    'last_page' => $articles->lastPage(),
                    'per_page' => $articles->perPage(),
                    'total' => $articles->total(),
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed fetching articles: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'filters' => $request->validated(),
            ]);
            return ResponseHelper::apiResponse(false, 'Failed to fetch articles', null, 500, [
                'error' => $e->getMessage()
            ]);
        }
    }
}
