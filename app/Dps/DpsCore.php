<?php
namespace App\Dps;

use Exception;
use App\Utils\AppLinkUtils;
use App\Logger\CloudLogger;

/**
 * Clase DpsCore
 * 
 * Proporciona métodos para interactuar con el sistema DPS a través de llamadas a un servidor externo.
 */
class DpsCore
{
    /**
     * Obtiene documentos en un rango de fechas con filtros específicos.
     *
     * @param  string  $startDate       Fecha de inicio en formato 'YYYY-MM-DD'.
     * @param  string  $endDate         Fecha de fin en formato 'YYYY-MM-DD'.
     * @param  int     $idUser          ID del usuario que solicita los documentos.
     * @param  object  $oSessionUser    Objeto de sesión del usuario autenticado.
     * @param  int     $statusFilter    Filtro de estado de los documentos. Por defecto es 0.
     * @param  int     $idCompany       ID de la compañía del usuario.
     * 
     * @return mixed   Datos obtenidos del servidor externo.
     * @throws Exception Si no se pueden obtener los documentos.
     */
    public static function getDocuments($startDate, $endDate, $idUser, $oSessionUser, $statusFilter, $idCompany)
    {
        // Obtener configuración del sistema
        $config = \App\Utils\Configuration::getConfigurations();
        $url = $config->AppLinkRoute . "/" . $config->AppLinkRouteGetDps;
        $method = "GET";
        $body = "";
        $requireAuth = true;
        $idCompany = $idCompany ?? 0;
        $parameters = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'id_user' => $idUser,
            'id_session_user' => $oSessionUser->external_id_n,
            'status_filter' => $statusFilter,
            'id_company' => $idCompany
        ];

        // Hacer la petición al servidor externo
        $rData = AppLinkUtils::requestAppLink($url, $method, $oSessionUser, $body, $requireAuth, $parameters);

        // Verificar si la respuesta es válida
        if ($rData->code != 200 && !$rData->data) {
            CloudLogger::log('error', json_encode($rData));
            throw new Exception("Error al obtener los documentos del servidor externo", 1);
        }

        return $rData->data;
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
    public static function getDocument($idYear, $idDocument, $oSessionUser, $idCompany)
    {
        // Obtener configuración del sistema
        $config = \App\Utils\Configuration::getConfigurations();
        $url = $config->AppLinkRoute . "/" . $config->AppLinkRouteGetDpsByPk;
        $idCompany = $idCompany ?? 0;
        $parameters = [
            'id_year' => $idYear,
            'id_doc' => $idDocument,
            'id_user' => $oSessionUser->external_id_n ?? 1,  // ID de usuario por defecto
            'id_company' => $idCompany
        ];

        // Hacer la petición al servidor externo
        $rData = AppLinkUtils::requestAppLink($url, "GET", $oSessionUser, "", true, $parameters);

        // Verificar si la respuesta es válida
        if (!$rData->data) {
            CloudLogger::log('error', json_encode($rData));
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
    public static function authorizeDps($idYear, $idDocument, $oSessionUser, $sComments, $idCompany)
    {
        // Obtener configuración del sistema
        $config = \App\Utils\Configuration::getConfigurations();
        $url = $config->AppLinkRoute . "/" . $config->AppLinkRouteAuthorizeDps;
        $method = "POST";
        $dataType = 2;
        $userId = $oSessionUser->external_id_n;
        $idCompany = $idCompany ?? 0;

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
            "idResource" => [$idYear, $idDocument],
            "dataType" => $dataType,
            "comment" => $sComments,
            "userId" => $userId,
            "idCompany" => $idCompany
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
    public static function rejectDps($idYear, $idDocument, $oSessionUser, $sComments, $idCompany)
    {
        // Obtener configuración del sistema
        $config = \App\Utils\Configuration::getConfigurations();
        $url = $config->AppLinkRoute . "/" . $config->AppLinkRouteRejectDps;
        $method = "POST";
        $dataType = 2;
        $userId = $oSessionUser->external_id_n;
        $idCompany = $idCompany ?? 0;

        // Crear JSON para el cuerpo de la solicitud
        $body = json_encode([
            "idResource" => [$idYear, $idDocument],
            "dataType" => $dataType,
            "comment" => $sComments,
            "userId" => $userId,
            "idCompany" => $idCompany
        ]);

        $requireAuth = true;
        $parameters = [];
        $jBody = json_decode($body);

        // Hacer la petición al servidor externo
        return AppLinkUtils::requestAppLink($url, $method, $oSessionUser, $jBody, $requireAuth, $parameters);
    }
}
