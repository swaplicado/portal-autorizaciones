<?php namespace App\Utils;

class folioUtils {
    public static function formatRequisitionsFolio($data){
        $config = \App\Utils\Configuration::getConfigurations();
        foreach ($data as $key => $value) {
            $data[$key]->idData = implode('-', $value->idData);
            $folio = $data[$key]->folio;
            for($i = $config->folio_size; $i > strlen($folio); $i--){
                $data[$key]->folio = '0'.$data[$key]->folio;
            }
        }

        return $data;
    }
}