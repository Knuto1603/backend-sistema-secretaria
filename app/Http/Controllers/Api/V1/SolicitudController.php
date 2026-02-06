<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Solicitud;
use App\Models\TipoSolicitud;
use App\Traits\ApiFilterable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SolicitudController extends Controller
{
    use ApiFilterable;

    /**
     * Procesa el registro de una nueva solicitud enviada vía FormData.
     */
    public function store(Request $request)
    {
        // 1. Validación de entrada (Laravel detecta archivos automáticamente en el Request)
        $request->validate([
            'programacion_id' => 'required|exists:programacion_academica,id',
            'motivo'          => 'required|string|min:20',
            'firma'           => 'required|string', // Se recibe como string Base64
            'archivo_sustento' => 'nullable|file|mimes:pdf,jpg,png|max:2048', // Máximo 2MB
        ]);

        return DB::transaction(function () use ($request) {
            $user = Auth::user();

            $tipoSolicitud = TipoSolicitud::where('codigo', 'CUPO_EXT')->first();

            if (!$tipoSolicitud) {
                return response()->json(['error' => 'Configuración de tipos de solicitud incompleta.'], 500);
            }

            // 3. Procesar la firma digital (Base64 a imagen física)
            $firmaPath = $this->storeBase64Signature($request->firma, $user->id);

            // 4. Procesar el archivo de sustento si existe
            $sustentoPath = null;
            $sustentoNombre = null;

            if ($request->hasFile('archivo_sustento')) {
                $file = $request->file('archivo_sustento');
                $sustentoNombre = $file->getClientOriginalName();
                // Guardamos en 'public/sustentos' para que sea accesible vía storage:link
                $sustentoPath = $file->store('sustentos', 'public');
            }

            // 5. Crear el registro en la base de datos
            $solicitud = Solicitud::create([
                'user_id'           => $user->id,
                'tipo_solicitud_id' => $tipoSolicitud->id,
                'programacion_id'   => $request->programacion_id,
                'motivo'            => $request->motivo,
                'firma_digital_path' => $firmaPath,
                'archivo_sustento_path' => $sustentoPath,
                'archivo_sustento_nombre' => $sustentoNombre,
                'estado'            => 'pendiente',
                'metadatos'         => [
                    'user_agent' => $request->userAgent(),
                    'ip'         => $request->ip(),
                ]
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Solicitud enviada exitosamente',
                'data'    => $solicitud
            ], 201);
        });
    }

    /**
     * Helper privado para decodificar y guardar la firma Base64.
     */
    private function storeBase64Signature(string $base64, int $userId): string
    {
        // Limpiar el prefijo data:image/png;base64,
        if (Str::contains($base64, ',')) {
            $base64 = explode(',', $base64)[1];
        }

        $decodedData = base64_decode($base64);
        $fileName = "firmas/signature_u{$userId}_" . now()->timestamp . ".png";

        Storage::disk('public')->put($fileName, $decodedData);

        return $fileName;
    }
}
