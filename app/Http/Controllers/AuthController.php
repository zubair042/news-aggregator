<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(Request $request): JsonResponse
    {
        try {
            $data = $this->authService->register($request->all());
            return ResponseHelper::apiResponse(true, 'User registered successfully', $data, 201);
        } catch (\Exception $e) {
            return ResponseHelper::apiResponse(
                false, 'An error occurred during registration', null, 500, ['error' => $e->getMessage()]
            );
        }
    }

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
