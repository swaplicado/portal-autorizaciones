<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Utils\AppLinkUtils;
use Illuminate\Http\Request;

class RequisitionsController extends Controller
{
    public function index(){
        $lResources = $this->getResources();
        
        return view('requisitions.requisitions')->with('lResources', $lResources);
    }

    public function getResources(){
        $data = AppLinkUtils::getResources(\Auth::user());
        $data = $data->lAuthData;
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

    public function approbeResource(Request $request){
        $config = \App\Utils\Configuration::getConfigurations();
        $idResource = $request->idResource;
        $dataType = $request->dataType;
        $authorize = 1;
        
        try {
            $body = '{
                "idResource": '.$idResource.',
                "dataType": '.$dataType.',
                "authorize": '.$authorize.',
                "user": "'.\Auth::user()->username.'"
            }';

            $result = AppLinkUtils::requestAppLink($config->AppLinkRouteAuthorizeResource, 'POST', \Auth::user(), $body);
            if(!is_null($result)){
                if($result->code != 200){
                    return json_encode(['success' => false, 'message' => $result->message, 'icon' => 'error']);
                }
            }else{
                return json_encode(['success' => false, 'message' => 'No se obtuvo respuesta desde AppLink', 'icon' => 'error']);
            }

            $lResources = $this->getResources();

        } catch (\Throwable $th) {
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'lResources' => $lResources, 'message' => $result->message, 'icon' => 'success']);
    }

    public function rejectResource(Request $request){
        $config = \App\Utils\Configuration::getConfigurations();
        $idResource = $request->idResource;
        $dataType = $request->dataType;
        $comment = $request->comment;
        $authorize = 0;
        
        try {
            $body = '{
                "idResource": '.$idResource.',
                "authorize": '.$authorize.',
                "dataType": '.$dataType.',
                "user": "'.\Auth::user()->username.'"
            }';

            $result = AppLinkUtils::requestAppLink($config->AppLinkRouteRejectResource, 'POST', \Auth::user(), $body);
            if(!is_null($result)){
                if($result->code != 200){
                    return json_encode(['success' => false, 'message' => $result->message, 'icon' => 'error']);
                }
            }else{
                return json_encode(['success' => false, 'message' => 'No se obtuvo respuesta desde AppLink', 'icon' => 'error']);
            }

            $lResources = $this->getResources();

        } catch (\Throwable $th) {
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'lResources' => $lResources, 'message' => $result->message, 'icon' => 'success']);
    }
}