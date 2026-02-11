<?php

namespace App\Http\Controllers\Api\V1;

use App\DTOs\Programacion\ImportProgramacionDTO;
use App\DTOs\Programacion\ProgramacionFilterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Programacion\ImportProgramacionRequest;
use App\Services\ProgramacionService;
use App\Transformers\ProgramacionTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class ProgramacionController extends Controller
{
    public function __construct(
        protected ProgramacionService $service,
        protected ProgramacionTransformer $transformer
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $dto = ProgramacionFilterDTO::fromRequest($request->all());
            $result = $this->service->getPaginated($dto, $request);

            $items = $this->transformer->collection(collect($result->items()));

            return $this->paginated($items, $result, 'Lista de programación académica');
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 404);
        }
    }

    public function show(string $id): JsonResponse
    {
        $programacion = $this->service->findById($id);

        if (!$programacion) {
            return $this->notFound('Programación no encontrada');
        }

        return $this->success($this->transformer->toArray($programacion));
    }

    public function import(ImportProgramacionRequest $request): JsonResponse
    {
        try {
            $dto = ImportProgramacionDTO::fromRequest(
                $request->file('file'),
                $request->periodo_id
            );

            $this->service->import($dto);

            return $this->success(null, 'Programación importada exitosamente');
        } catch (Exception $e) {
            return $this->error('Error al procesar el Excel: ' . $e->getMessage(), 500);
        }
    }
}
