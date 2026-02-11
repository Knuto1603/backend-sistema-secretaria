<?php

namespace App\Repositories\Eloquent;

use App\Models\ProgramacionAcademica;
use App\Repositories\Contracts\ProgramacionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ProgramacionRepository implements ProgramacionRepositoryInterface
{
    public function __construct(
        protected ProgramacionAcademica $model
    ) {}

    public function getByPeriodoWithFilters(string $periodoId, ?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->getBaseQuery($periodoId);

        if ($search) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('clave', 'like', "%{$search}%")
                    ->orWhere('grupo', 'like', "%{$search}%")
                    ->orWhereHas('curso', function (Builder $q) use ($search) {
                        $q->where('nombre', 'like', "%{$search}%")
                            ->orWhere('codigo', 'like', "%{$search}%");
                    })
                    ->orWhereHas('docente', function (Builder $q) use ($search) {
                        $q->where('nombre_completo', 'like', "%{$search}%");
                    });
            });
        }

        return $query->latest()->paginate($perPage);
    }

    public function findById(string $id): ?ProgramacionAcademica
    {
        return $this->model
            ->with(['curso.area', 'docente', 'periodo'])
            ->find($id);
    }

    public function deleteByPeriodo(string $periodoId): int
    {
        return $this->model->where('periodo_id', $periodoId)->delete();
    }

    public function getBaseQuery(string $periodoId): Builder
    {
        return $this->model
            ->with(['curso.area', 'docente', 'periodo'])
            ->where('periodo_id', $periodoId);
    }
}
