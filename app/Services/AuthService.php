<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Registers a new user
     *
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function register(array $data): array
    {
        try {
            $existingUser = $this->userRepository->findByEmail($data['email']);
            if ($existingUser) {
                throw ValidationException::withMessages(['email' => 'Email already exists.']);
            }

            $user = $this->userRepository->create($data);
            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'user'         => $user,
                'access_token' => $token,
                'token_type'   => 'Bearer',
            ];
        } catch (\Throwable $e) {
            Log::error('User registration failed', [
                'input' => $data,
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Registration failed: ' . $e->getMessage());
        }
    }

    /**
     * Authenticates a user
     *
     * @param array $data
     * @return array|null
     * @throws \Exception
     */
    public function login(array $data): ?array
    {
        try {
            $user = $this->userRepository->findByEmail($data['email']);

            if (!$user || !$this->userRepository->validatePassword($user, $data['password'])) {
                return null;
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'user'         => $user,
                'access_token' => $token,
                'token_type'   => 'Bearer',
            ];
        } catch (\Throwable $e) {
            Log::error('Login failed', [
                'email' => $data['email'] ?? null,
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Login failed: ' . $e->getMessage());
        }
    }

    /**
     * Verifies the current password and updates it
     *
     * @param $user
     * @param string $currentPassword
     * @param string $newPassword
     * @return bool
     * @throws \Exception
     */
    public function updatePassword($user, string $currentPassword, string $newPassword): bool
    {
        try {
            if (!$this->userRepository->validatePassword($user, $currentPassword)) {
                return false;
            }

            return $this->userRepository->updatePassword($user, $newPassword);
        } catch (\Throwable $e) {
            Log::error('Password update failed', [
                'user_id' => $user->id ?? null,
                'error'   => $e->getMessage(),
            ]);
            throw new \Exception('Failed to update password: ' . $e->getMessage());
        }
    }
}
