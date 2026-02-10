<?php

namespace App\Repositories\Eloquent;

use App\Models\Solicitud;
use App\Repositories\Contracts\SolicitudRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SolicitudRepository implements SolicitudRepositoryInterface
{
    public function __construct(
        protected Solicitud $model
    ) {}

    public function create(array $data): Solicitud
    {
        return $this->model->create($data);
    }

    public function findById(string $id): ?Solicitud
    {
        return $this->model
            ->with(['user', 'tipoSolicitud', 'programacion.curso', 'programacion.docente'])
            ->find($id);
    }

    public function findByUserId(string $userId): LengthAwarePaginator
    {
        return $this->model
            ->with(['tipoSolicitud', 'programacion.curso', 'programacion.docente'])
            ->where('user_id', $userId)
            ->latest()
            ->paginate(10);
    }

    public function getPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->model
            ->with(['user', 'tipoSolicitud', 'programacion.curso']);

        if (isset($filters['estado'])) {
            $query->where('estado', $filters['estado']);
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        return $query->latest()->paginate($perPage);
    }
}
