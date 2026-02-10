<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\DTOs\Auth\LoginDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $dto = LoginDTO::fromRequest($request->validated());
        $response = $this->authService->login($dto);

        return $this->success($response->toArray(), 'Inicio de sesión exitoso');
    }

    public function me(Request $request): JsonResponse
    {
        $userDTO = $this->authService->getAuthenticatedUser($request->user());

        return $this->success($userDTO->toArray(), 'Usuario autenticado');
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return $this->success(null, 'Sesión cerrada exitosamente');
    }
}
