<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserPreferenceRequest;
use App\Models\UserPreference;
use App\Services\UserPreferenceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPreferenceController extends Controller
{
    private UserPreferenceService $preferenceService;

    public function __construct(UserPreferenceService $preferenceService)
    {
        $this->preferenceService = $preferenceService;
    }

    public function update(UserPreferenceRequest $request)
    {
        $user = Auth::user();
        $preferences = $this->preferenceService->updatePreferences($user->id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Preferences updated successfully.',
            'data' => $preferences,
        ]);
    }
}
