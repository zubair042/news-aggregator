<?php

namespace App\Repositories;

use App\Models\UserPreference;

class UserPreferenceRepository
{
    private UserPreference $userPreference;

    public function __construct()
    {
        $this->userPreference = new UserPreference();
    }

    /**
     * Create or update user preferences in the database.
     *
     * @param int $userId
     * @param array $data
     * @return UserPreference
     */
    public function updatePreferences(int $userId, array $data): UserPreference
    {
        return $this->userPreference->updateOrCreate(
            ['user_id' => $userId],
            [
                'sources' => $data['sources'] ?? [],
                'categories' => $data['categories'] ?? [],
                'authors' => $data['authors'] ?? [],
            ]
        );
    }

    /**
     * Get Preference detail by User id
     *
     * @param int $userId
     * @return UserPreference|null
     */
    public function getByUserId(int $userId): ?UserPreference
    {
        return $this->userPreference->where('user_id', $userId)->first();
    }
}
