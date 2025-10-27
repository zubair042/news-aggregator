<?php

namespace App\Services;

use App\Models\UserPreference;
use App\Repositories\UserPreferenceRepository;

class UserPreferenceService
{
    protected UserPreferenceRepository $preferenceRepository;

    public function __construct(UserPreferenceRepository $preferenceRepository)
    {
        $this->preferenceRepository = $preferenceRepository;
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
        return $this->preferenceRepository->updatePreferences($userId, $data);
    }

    public function getPreferences(int $userId): ?UserPreference
    {
        return $this->preferenceRepository->getByUserId($userId);
    }

}
