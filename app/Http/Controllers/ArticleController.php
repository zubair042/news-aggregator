<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleFilterRequest;
use App\Services\ArticleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    protected ArticleService $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    public function index(ArticleFilterRequest $request): JsonResponse
    {
        $user = Auth::user();

        $articles = $this->articleService->getArticles($request->validated(), $user);

        return response()->json([
            'success' => true,
            'message' => 'Articles fetched successfully.',
            'data' => $articles,
        ]);
    }
}
