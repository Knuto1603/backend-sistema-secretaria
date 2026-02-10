<?php

namespace App\DTOs\Auth;

use App\Models\User;
use Illuminate\Support\Collection;

class AuthenticatedUserDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $email,
        public readonly Collection $roles,
        public readonly Collection $permissions
    ) {}

    public static function fromUser(User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            roles: $user->getRoleNames(),
            permissions: $user->getAllPermissions()->pluck('name')
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'roles' => $this->roles,
            'permissions' => $this->permissions,
        ];
    }
}
