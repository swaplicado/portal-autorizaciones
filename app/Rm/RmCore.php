<?php
namespace App\Rm;

use Exception;
use App\Utils\AppLinkUtils;
use Log;

/**
 * Clase DpsCore
 * 
 * Proporciona métodos para interactuar con el sistema DPS a través de llamadas a un servidor externo.
 */
class RmCore
{
    /**
     * Obtiene documentos en un rango de fechas con filtros específicos.
     *
     * @param  string  $startDate       Fecha de inicio en formato 'YYYY-MM-DD'.
     * @param  string  $endDate         Fecha de fin en formato 'YYYY-MM-DD'.
     * @param  int     $idUser          ID del usuario que solicita los documentos.
     * @param  object  $oSessionUser    Objeto de sesión del usuario autenticado.
     * @param  int     $statusFilter    Filtro de estado de los documentos. Por defecto es 0.
     * @return mixed   Datos obtenidos del servidor externo.
     * @throws Exception Si no se pueden obtener los documentos.
     */
    public static function getDocuments($startDate, $endDate, $idUser, $oSessionUser, $statusFilter = 0)
    {
        // Obtener configuración del sistema
        $config = \App\Utils\Configuration::getConfigurations();
        $url = $config->AppLinkRoute . "/" . $config->AppLinkRouteGetRm;
        $method = "GET";
        $body = "";
        $requireAuth = true;
        $idCompany = 0;
        $parameters = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'id_user' => $idUser,
            'id_session_user' => $oSessionUser->external_id_n,
            'status_filter' => $statusFilter,
            'id_company' => $idCompany
        ];

        // Imprimir parámetros:
        Log::info("Parámetros de la petición:");
        Log::info(json_encode($parameters));
        Log::info("Antes de la petición");

        // Hacer la petición al servidor externo
        $rData = AppLinkUtils::requestAppLink($url, $method, $oSessionUser, $body, $requireAuth, $parameters);
        Log::info("Respuesta del servidor:");
        Log::info(json_encode($rData));

        // Verificar si la respuesta es válida
        if (is_null($rData)) {
            throw new Exception("No se pudo conectar al servidor externo (AppLink)", 1);
        }

        // Obtener el código de respuesta de forma segura
        $code = is_array($rData) ? $rData['code'] : (is_object($rData) ? $rData->code ?? $rData->get('code') : null);
        Log::info($rData->code);
        Log::info($code);
        if ($code != 200) {
            Log::error("Error al obtener los documentos del servidor externo. Código: " . $code);
            throw new Exception("Error al obtener los documentos del servidor externo", 1);
        }

        // Obtener data de forma segura
        $data = is_array($rData) ? $rData['data'] : (is_object($rData) ? $rData->data ?? $rData->get('data') : null);

        return $data;
    }

    /**
     * Obtiene un documento específico por su ID y año.
     *
     * @param  int     $idYear       Año del documento.
     * @param  int     $idDocument   ID del documento.
     * @param  object  $oSessionUser Objeto de sesión del usuario autenticado.
     * @return mixed   Datos obtenidos del servidor externo.
     * @throws Exception Si no se puede obtener el documento.
     */
    public static function getDocument($idMaterialRequest, $oSessionUser)
    {
        // Obtener configuración del sistema
        $config = \App\Utils\Configuration::getConfigurations();
        $url = $config->AppLinkRoute . "/" . $config->AppLinkRouteGetRmByPk;
        $parameters = [
            'id_mat_req' => $idMaterialRequest
        ];

        // Hacer la petición al servidor externo
        $rData = AppLinkUtils::requestAppLink($url, "GET", $oSessionUser, "", true, $parameters);

        // Verificar si la respuesta es válida
        if (!$rData->data) {
            throw new Exception("Error al obtener el documento del servidor externo", 1);
        }

        return $rData->data;
    }

    /**
     * Autoriza un documento DPS.
     *
     * @param  int     $idYear       Año del documento.
     * @param  int     $idDocument   ID del documento.
     * @param  object  $oSessionUser Objeto de sesión del usuario autenticado.
     * @param  string  $sComments    Comentarios sobre la autorización.
     * @return mixed   Respuesta del servidor externo.
     */
    public static function authorizeRm($idDocument, $oSessionUser, $sComments)
    {
        // Obtener configuración del sistema
        $config = \App\Utils\Configuration::getConfigurations();
        $url = $config->AppLinkRoute . "/" . $config->AppLinkRouteAuthorizeDps;
        $method = "POST";
        $dataType = 1;
        $userId = $oSessionUser->external_id_n;

        // Validar si el usuario tiene una cuenta asociada
        if (!$userId) {
            $code = 500;
            return json_encode([
                "code" => $code,
                "message" => "El usuario de la sesión no tiene un usuario en SIIE."
            ]);
        }

        // Crear JSON para el cuerpo de la solicitud
        $body = json_encode([
            "idResource" => [$idDocument],
            "dataType" => $dataType,
            "comment" => $sComments,
            "userId" => $userId
        ]);

        $requireAuth = true;
        $parameters = [];
        $jBody = json_decode($body);

        // Hacer la petición al servidor externo
        return AppLinkUtils::requestAppLink($url, $method, $oSessionUser, $jBody, $requireAuth, $parameters);
    }

    /**
     * Rechaza un documento DPS.
     *
     * @param  int     $idYear       Año del documento.
     * @param  int     $idDocument   ID del documento.
     * @param  object  $oSessionUser Objeto de sesión del usuario autenticado.
     * @param  string  $sComments    Comentarios sobre el rechazo.
     * @return mixed   Respuesta del servidor externo.
     */
    public static function rejectRm($idDocument, $oSessionUser, $sComments)
    {
        // Obtener configuración del sistema
        $config = \App\Utils\Configuration::getConfigurations();
        $url = $config->AppLinkRoute . "/" . $config->AppLinkRouteRejectDps;
        $method = "POST";
        $dataType = 1;
        $userId = $oSessionUser->external_id_n;

        // Crear JSON para el cuerpo de la solicitud
        $body = json_encode([
            "idResource" => [$idDocument],
            "dataType" => $dataType,
            "comment" => $sComments,
            "userId" => $userId
        ]);

        $requireAuth = true;
        $parameters = [];
        $jBody = json_decode($body);

        // Hacer la petición al servidor externo
        return AppLinkUtils::requestAppLink($url, $method, $oSessionUser, $jBody, $requireAuth, $parameters);
    }
}