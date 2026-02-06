<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ProgramacionAcademica;
use App\Models\Periodo;
use App\Imports\ProgramacionImport;
use App\Traits\ApiFilterable;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

class ProgramacionController extends Controller
{
    // Usamos el Trait dentro de la clase
    use ApiFilterable;

    /**
     * Lista la programación académica con paginación y filtros reutilizables.
     */
    public function index(Request $request)
    {
        $periodoId = $request->periodo_id ?? Periodo::where('activo', true)->value('id');

        if (!$periodoId) {
            return response()->json(['error' => 'No hay un periodo académico activo.'], 404);
        }

        // Preparamos la query base
        $query = ProgramacionAcademica::with(['curso.area', 'docente', 'periodo'])
            ->where('periodo_id', $periodoId);

        return $this->applyFiltersAndPaginate(
            $query,
            $request,
            ['clave', 'grupo'], // Campos locales de ProgramacionAcademica
            [
                'curso' => ['nombre', 'codigo'], // Campos de la relación 'curso'
                'docente' => ['nombre_completo'] // Campos de la relación 'docente'
            ]
        );
    }

    /**
     * Importar programación desde Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'periodo_id' => 'nullable|exists:periodos,id'
        ]);

        $periodoId = $request->periodo_id ?? Periodo::where('activo', true)->value('id');

        if (!$periodoId) {
            return response()->json(['error' => 'No se pudo determinar el periodo académico.'], 422);
        }

        try {
            ProgramacionAcademica::where('periodo_id', $periodoId)->delete();
            Excel::import(new ProgramacionImport($periodoId), $request->file('file'));

            return response()->json(['message' => 'Programación importada exitosamente.']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al procesar el Excel: ' . $e->getMessage()], 500);
        }
    }

    public function getPeriodos() {
        return response()->json(Periodo::orderBy('nombre', 'desc')->get());
    }

    public function show($id) {
        return ProgramacionAcademica::with(['curso.area', 'docente', 'periodo'])->findOrFail($id);
    }
}
