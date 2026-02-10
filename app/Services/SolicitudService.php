<?php

namespace App\Services;

use App\DTOs\Solicitud\CreateSolicitudDTO;
use App\Models\Solicitud;
use App\Models\User;
use App\Repositories\Contracts\SolicitudRepositoryInterface;
use App\Repositories\Contracts\TipoSolicitudRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;

class SolicitudService
{
    public function __construct(
        protected SolicitudRepositoryInterface $solicitudRepository,
        protected TipoSolicitudRepositoryInterface $tipoSolicitudRepository
    ) {}

    public function create(CreateSolicitudDTO $dto, User $user): Solicitud
    {
        return DB::transaction(function () use ($dto, $user) {
            $tipoSolicitud = $this->tipoSolicitudRepository->findByCode('CUPO_EXT');

            if (!$tipoSolicitud) {
                throw new Exception('Configuración de tipos de solicitud incompleta.');
            }

            $firmaPath = $this->storeBase64Signature($dto->firma, $user->id);

            $sustentoPath = null;
            $sustentoNombre = null;

            if ($dto->archivo_sustento) {
                $sustentoNombre = $dto->archivo_sustento->getClientOriginalName();
                $sustentoPath = $dto->archivo_sustento->store('sustentos', 'public');
            }

            return $this->solicitudRepository->create([
                'user_id' => $user->id,
                'tipo_solicitud_id' => $tipoSolicitud->id,
                'programacion_id' => $dto->programacion_id,
                'motivo' => $dto->motivo,
                'firma_digital_path' => $firmaPath,
                'archivo_sustento_path' => $sustentoPath,
                'archivo_sustento_nombre' => $sustentoNombre,
                'estado' => 'pendiente',
                'metadatos' => [
                    'user_agent' => $dto->user_agent,
                    'ip' => $dto->ip,
                ]
            ]);
        });
    }

    public function getByUser(User $user): LengthAwarePaginator
    {
        return $this->solicitudRepository->findByUserId($user->id);
    }

    public function findById(string $id): ?Solicitud
    {
        return $this->solicitudRepository->findById($id);
    }

    public function getPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->solicitudRepository->getPaginated($filters, $perPage);
    }

    protected function storeBase64Signature(string $base64, string $userId): string
    {
        if (Str::contains($base64, ',')) {
            $base64 = explode(',', $base64)[1];
        }

        $decodedData = base64_decode($base64);
        $fileName = "firmas/signature_u{$userId}_" . now()->timestamp . ".png";

        Storage::disk('public')->put($fileName, $decodedData);

        return $fileName;
    }
}
