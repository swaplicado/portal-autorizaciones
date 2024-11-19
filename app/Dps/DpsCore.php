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
        $oData = json_decode($rData->data);
        if (! $oData->lDocuments) {
            throw new Exception("Error al obtener los documentos del servidor externo", 1);
        }

        return $oData->lDocuments;
    }
}