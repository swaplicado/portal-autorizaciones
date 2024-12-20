<?php
namespace App\Dps;

use Exception;
use App\Utils\AppLinkUtils;

class DpsCore
{

    public static function getDocuments($startDate, $endDate, $idUser, $oSessionUser, $statusFilter = 0)
    {
        $config = \App\Utils\Configuration::getConfigurations();
        $url = $config->AppLinkRoute . "/" . $config->AppLinkRouteGetDps;
        $method = "GET";
        $body = "";
        $requireAuth = true;
        $parameters = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'id_user' => $idUser,
            'status_filter' => $statusFilter
        ];

        $rData = AppLinkUtils::requestAppLink($url, $method, $oSessionUser, $body, $requireAuth, $parameters);
        // $oData = json_decode($rData->data);
        if ($rData->code != 200 && !$rData->data) {
            throw new Exception("Error al obtener los documentos del servidor externo", 1);
        }

        return $rData->data;
    }

    public static function getDocument($idYear, $idDocument, $oSessionUser)
    {
        $config = \App\Utils\Configuration::getConfigurations();
        $url = $config->AppLinkRoute . "/" . $config->AppLinkRouteGetDpsByPk;
        $method = "GET";
        $body = "";
        $requireAuth = true;
        $idUser = 10;
        $parameters = [
            'id_year' => $idYear,
            'id_doc' => $idDocument,
            'id_user' => $idUser
        ];

        $rData = AppLinkUtils::requestAppLink($url, $method, $oSessionUser, $body, $requireAuth, $parameters);
        // $oData = json_decode($rData->data);
        if (!$rData->data) {
            throw new Exception("Error al obtener el documento del servidor externo", 1);
        }

        return $rData->data;
    }

    public static function authorizeDps($idYear, $idDocument, $oSessionUser, $sComments)
    {
        $config = \App\Utils\Configuration::getConfigurations();
        $url = $config->AppLinkRoute . "/" . $config->AppLinkRouteAuthorizeDps;
        $method = "POST";
        $dataType = 2;
        $userId = $oSessionUser->external_id_n;
        // $userId = 76;
        if (! $userId) {
            $code = 500;
            return '{
                "code": '.$code.',
                "message": "El usuario de la sesión no tiene un usuario en siie."
            }';
        }
        # Crear json para body:
        $body = '{
            "idResource": ['.$idYear.', '.$idDocument.'],
            "dataType": '.$dataType.',
            "comment": "'.$sComments.'",
            "userId": '.$userId.'
        }';
        $requireAuth = true;
        $parameters = [];
        $jBody = json_decode($body);

        $rData = AppLinkUtils::requestAppLink($url, $method, $oSessionUser, $jBody, $requireAuth, $parameters);

        return json_encode($rData);
    }

    public static function rejectDps($idYear, $idDocument, $oSessionUser, $sComments)
    {
        $config = \App\Utils\Configuration::getConfigurations();
        $url = $config->AppLinkRoute . "/" . $config->AppLinkRouteRejectDps;
        $method = "POST";
        $dataType = 2;
        $userId = $oSessionUser->external_id_n;
        // $userId = 172;
        # Crear json para body:
        $body = '{
            "idResource": ['.$idYear.', '.$idDocument.'],
            "dataType": '.$dataType.',
            "comment": "'.$sComments.'",
            "userId": '.$userId.'
        }';
        $requireAuth = true;
        $parameters = [];
        $jBody = json_decode($body);

        $rData = AppLinkUtils::requestAppLink($url, $method, $oSessionUser, $jBody, $requireAuth, $parameters);

        return json_encode($rData);
    }
}