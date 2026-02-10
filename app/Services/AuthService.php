<?php

namespace App\Services;

use App\DTOs\Auth\AuthenticatedUserDTO;
use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\LoginResponseDTO;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {}

    public function login(LoginDTO $dto): LoginResponseDTO
    {
        $user = $this->userRepository->findByEmail($dto->email);

        if (!$user || !Hash::check($dto->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        $token = $user->createToken($dto->device_name)->plainTextToken;

        return new LoginResponseDTO(
            status: 'success',
            access_token: $token,
            token_type: 'Bearer',
            user: AuthenticatedUserDTO::fromUser($user)
        );
    }

    public function getAuthenticatedUser(User $user): AuthenticatedUserDTO
    {
        return AuthenticatedUserDTO::fromUser($user);
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}
