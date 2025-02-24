<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Pages\DPSController;

/**
 * Controlador API para la gestión de documentos DPS.
 * 
 * Esta clase proporciona métodos para interactuar con el sistema DPS a través de la API.
 */
class DPSApiController extends Controller
{
    /**
     * Prueba de conexión a la API.
     *
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con un mensaje de prueba.
     */
    public function test()
    {
        return response()->json(['message' => 'Esto es una prueba'], 200);
    }

    /**
     * Obtiene documentos dentro de un rango específico.
     *
     * @param  \Illuminate\Http\Request  $request  Objeto de solicitud HTTP con los parámetros necesarios.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con los documentos obtenidos.
     */
    public function getDocumentsInRange(Request $request)
    {
        // Crear una instancia del controlador DPS y delegar la petición.
        $dpsController = new DpsController();
        return $dpsController->getDocumentsInRange($request);
    }

    /**
     * Obtiene un documento específico basado en los parámetros proporcionados.
     *
     * @param  \Illuminate\Http\Request  $request  Objeto de solicitud HTTP con los parámetros necesarios.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con los datos del documento.
     */
    public function getDocument(Request $request)
    {
        // Obtener los parámetros de la solicitud
        $idYear = $request->input('idYear');
        $idDoc = $request->input('idDoc');

        // Crear una instancia del controlador DPS y delegar la petición.
        $dpsController = new DpsController();
        return $dpsController->getDocument($request, $idYear, $idDoc);
    }

    /**
     * Autoriza un documento DPS.
     *
     * @param  \Illuminate\Http\Request  $request  Objeto de solicitud HTTP con los parámetros necesarios.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado de la autorización.
     */
    public function authorizeDps(Request $request)
    {
        // Crear una instancia del controlador DPS y delegar la petición.
        $dpsController = new DpsController();

        // Obtener los parámetros de la solicitud
        $idYear = $request->input('idYear');
        $idDoc = $request->input('idDoc');

        return $dpsController->authorizeDps($request, $idYear, $idDoc);
    }

    /**
     * Rechaza un documento DPS.
     *
     * @param  \Illuminate\Http\Request  $request  Objeto de solicitud HTTP con los parámetros necesarios.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado del rechazo.
     */
    public function rejectDps(Request $request)
    {
        // Crear una instancia del controlador DPS y delegar la petición.
        $dpsController = new DpsController();

        // Obtener los parámetros de la solicitud
        $idYear = $request->input('idYear');
        $idDoc = $request->input('idDoc');

        return $dpsController->rejectDps($request, $idYear, $idDoc);
    }
}
