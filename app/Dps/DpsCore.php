<?php namespace App\Dps;

use Exception;
use App\Utils\AppLinkUtils;

class DpsCore {

    public static function getDocuments($startDate, $endDate, $idUser, $oSessionUser) {
        $config = \App\Utils\Configuration::getConfigurations();
        $url = $config->AppLinkRoute."/".$config->AppLinkRouteGetDps;
        $method = "GET";
        $body = "";
        $requireAuth = true;
        $parameters = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'id_user' => $idUser
        ];

        $rData = AppLinkUtils::requestAppLink($url, $method, $oSessionUser, $body, $requireAuth, $parameters);
        // $oData = json_decode($rData->data);
        if (! $rData->data) {
            throw new Exception("Error al obtener los documentos del servidor externo", 1);
        }

        return $rData->data;
    }

    public static function getDocument($idYear, $idDocument, $oSessionUser) {
        $config = \App\Utils\Configuration::getConfigurations();
        $url = $config->AppLinkRoute."/".$config->AppLinkRouteGetDpsByPk;
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
        if (! $rData->data) {
            throw new Exception("Error al obtener el documento del servidor externo", 1);
        }

        return $rData->data;
    }
}