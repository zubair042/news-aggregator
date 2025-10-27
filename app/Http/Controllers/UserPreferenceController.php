<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
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
    public function setPreferences(UserPreferenceRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $preferences = $this->preferenceService->updatePreferences($user->id, $request->validated());

            return ResponseHelper::apiResponse(
                true,
                'Preferences updated successfully.',
                new UserPreferenceResource($preferences)
            );
        } catch (\Throwable $e) {
            Log::error('Failed to update user preferences: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'input' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);

            return ResponseHelper::apiResponse(
                false,
                'Failed to update preferences.',
                null,
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Retrieve the authenticated user's news preferences.
     *
     * @return JsonResponse
     */
    public function getPreferences(): JsonResponse
    {
        try {
            $user = Auth::user();
            $preferences = $this->preferenceService->getPreferences($user->id);

            if (!$preferences) {
                return ResponseHelper::apiResponse(
                    true,
                    'No preferences found.',
                    null
                );
            }

            return ResponseHelper::apiResponse(
                true,
                'Preferences retrieved successfully.',
                new UserPreferenceResource($preferences)
            );
        } catch (\Throwable $e) {
            Log::error('Failed to retrieve user preferences: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString(),
            ]);

            return ResponseHelper::apiResponse(
                false,
                'Failed to retrieve preferences.',
                null,
                500,
                $e->getMessage()
            );
        }
    }


}
