<?php


namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    private User $user;

    public function __construct()
    {
        $this->user = new User();
    }

    /**
     * Create a new user record.
     *
     * @param array $data
     * @return User
     */
    public function create(array $data): User
    {
        return $this->user->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * Find user by email address.
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return $this->user->where('email', $email)->first();
    }

    /**
     * Update user password.
     *
     * @param User $user
     * @param string $newPassword
     * @return bool
     */
    public function updatePassword(User $user, string $newPassword): bool
    {
        return $user->update([
            'password' => Hash::make($newPassword),
        ]);
    }

    /**
     * Validate password against stored hash.
     *
     * @param User $user
     * @param string $plainPassword
     * @return bool
     */
    public function validatePassword(User $user, string $plainPassword): bool
    {
        return Hash::check($plainPassword, $user->password);
    }
}
