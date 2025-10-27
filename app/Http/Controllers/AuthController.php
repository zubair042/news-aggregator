<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handles user registration
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $data = $this->authService->register($request->all());
            return ResponseHelper::apiResponse(true, 'User registered successfully', $data, 201);
        } catch (ValidationException $e) {
            return ResponseHelper::apiResponse(false, $e->getMessage(), null, 422, $e->errors());
        } catch (\Throwable $e) {
            return ResponseHelper::apiResponse(false, 'Registration failed', null, 500, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Authenticates the user
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $data = $this->authService->login($request->all());
            if (!$data) {
                return ResponseHelper::apiResponse(false, 'The provided credentials are incorrect.', null, 401);
            }
            return ResponseHelper::apiResponse(true, 'Login successful', $data);
        } catch (\Exception $e) {
            return ResponseHelper::apiResponse(false, 'An error occurred during login', null, 500, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Updates the authenticated user's password
     *
     * @param UpdatePasswordRequest $request
     * @return JsonResponse
     */
    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        try {
            $updated = $this->authService->updatePassword(
                $request->user(),
                $request->current_password,
                $request->new_password
            );

            if (!$updated) {
                return ResponseHelper::apiResponse(false, 'The current password is incorrect.', null, 403);
            }

            return ResponseHelper::apiResponse(true, 'Password has been updated successfully');
        } catch (\Exception $e) {
            return ResponseHelper::apiResponse(false, 'An error occurred while updating the password', null, 500, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Logs out the authenticated user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->tokens()->delete();
            return ResponseHelper::apiResponse(true, 'Successfully logged out');
        } catch (\Exception $e) {
            return ResponseHelper::apiResponse(false, 'An error occurred during logout', null, 500, ['error' => $e->getMessage()]);
        }
    }
}
