<?php

namespace App\Repositories\Contracts;

use App\Models\Solicitud;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SolicitudRepositoryInterface
{
    public function create(array $data): Solicitud;

    public function findById(string $id): ?Solicitud;

    public function findByUserId(string $userId): LengthAwarePaginator;

    public function getPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator;
}
