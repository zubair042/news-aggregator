<?php

namespace App\Services;

use App\Models\UserPreference;

class UserPreferenceService
{
    /**
     * @param int $userId
     * @param array $data
     * @return UserPreference
     */
    public function updatePreferences(int $userId, array $data): UserPreference
    {
        return UserPreference::updateOrCreate(
            ['user_id' => $userId],
            [
                'sources' => $data['sources'] ?? [],
                'categories' => $data['categories'] ?? [],
                'authors' => $data['authors'] ?? [],
            ]
        );
    }
}
