<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Curso;
use App\Models\Solicitud;
use App\Traits\ApiFilterable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CursoController
{
    use ApiFilterable;

    public function index(Request $request)
    {
        $query = Curso::select('cursos.*');
        return $this->applyFiltersAndPaginate($query, $request,[ 'nombre', 'codigo'
        ]);
    }

    public function show($id)
    {
        $curso = Curso::find($id);

        if (!$curso) {
            return response()->json(['error' => 'Curso no encontrado.'], 404);
        }

        return response()->json($curso);
    }
}
