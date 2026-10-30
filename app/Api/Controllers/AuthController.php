<?php

namespace App\Api\Controllers;

use App\Api\Resources\UserResource;
use App\Domain\Interfaces\Services\IAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AuthController extends Controller
{
    public function __construct(private IAuthService $auth)
    {
    }

    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $result = $this->auth->register($data);

        return response()->json([
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $result = $this->auth->login($data);

        return response()->json([
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
        ]);
    }

    public function logout(): JsonResponse
    {
        $this->auth->logout();

        return response()->json(['message' => 'Sesión cerrada correctamente.']);
    }

    public function me(): UserResource
    {
        return new UserResource($this->auth->me());
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        return response()->json([
            'message' => $this->auth->forgotPassword($data['email']),
        ]);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $this->auth->resetPassword($data);

        return response()->json(['message' => 'Contraseña restablecida correctamente.']);
    }
}
