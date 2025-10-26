<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserPreferenceRequest;
use App\Http\Resources\UserPreferenceResource;
use App\Services\UserPreferenceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserPreferenceController extends Controller
{
    private UserPreferenceService $preferenceService;

    public function __construct(UserPreferenceService $preferenceService)
    {
        $this->preferenceService = $preferenceService;
    }

    /**
     * Update the authenticated user's news preferences.
     *
     * @param UserPreferenceRequest $request
     * @return JsonResponse
     */
    public function update(UserPreferenceRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $preferences = $this->preferenceService->updatePreferences($user->id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Preferences updated successfully.',
                'data' => new UserPreferenceResource($preferences),
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to update user preferences: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'input' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update preferences.',
                'data' => null,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
