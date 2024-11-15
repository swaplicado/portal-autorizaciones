<?php

namespace App\Http\Controllers\Pages;

use App\Constants\SysConst;
use App\Http\Controllers\Controller;
use App\Utils\AppLinkUtils;
use App\Utils\dateUtils;
use App\Utils\folioUtils;
use App\Utils\formatersUtils;
use Illuminate\Http\Request;

class RequisitionsController extends Controller
{
    public function index(){
        // $lResources = $this->getResources();

        try {
            $data = AppLinkUtils::getResources(\Auth::user());
    
            $lResources = [];
            $message = "";
            $code = $data->code;
            if($data->code != 200){
                $message = $data->message;
            }else{
                $oData = json_decode($data->data);
                $lResources = folioUtils::formatRequisitionsFolio($oData->lAuthData);
                $loResources = collect($lResources)->sortByDesc('date')->sortByDesc('folio');
                $lResources = $loResources->values()->all();
                foreach($lResources as $oRes){
                    $oRes->date = dateUtils::formatDate($oRes->date, 'D-m-Y');
                }
            }
    
            $lStatus = SysConst::lAuthStatus;
            array_push($lStatus, ['id' => 0, 'text' => 'Todos']);
            // array_splice($lStatus, 0, 0, array(['id' => 0, 'text' => 'Todos']));
            $lTypes = SysConst::lTypes;
            // array_splice($lTypes, 0, 0, array(['id' => 0, 'text' => 'Todos']));
        } catch (\Throwable $th) {
            \Log::error($th);
            return view('errorPages.serverError');
        }
        
        return view('requisitions.requisitions')->with('lResources', $lResources)
                                                ->with('lStatus', $lStatus)
                                                ->with('lTypes', $lTypes)
                                                ->with('code', $code)
                                                ->with('message', $message);
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
        if(is_null(\Auth::user()->external_id_n)){
            return json_encode(['success' => false, 'message' => 'Este usuario no puede autorizar en el sistema externo', 'icon' => 'error']);
        }
        
        $config = \App\Utils\Configuration::getConfigurations();
        $idResource = $request->idResource;
        $dataType = $request->dataType;
        $authorize = 1;
        
        try {
            $body = '{
                "idResource": '.$idResource.',
                "dataType": '.$dataType.',
                "authorize": '.$authorize.',
                "userId": '.\Auth::user()->external_id_n.',
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

            // $lResources = $this->getResources();

            $data = AppLinkUtils::getResources(\Auth::user());

            $lResources = [];
            $message = "";
            if($data->code != 200){
                $message = $data->message;
                \Log::error($message);
                return json_encode(['success' => false, 'message' => $message, 'icon' => 'error']);
            }

            $oData = json_decode($data->data);
            $lResources = folioUtils::formatRequisitionsFolio($oData->lAuthData);
            foreach($lResources as $oRes){
                $oRes->date = dateUtils::formatDate($oRes->date, 'D-m-Y');
            }

        } catch (\Throwable $th) {
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'lResources' => $lResources, 'message' => $result->message, 'icon' => 'success']);
    }

    public function rejectResource(Request $request){
        if(is_null(\Auth::user()->external_id_n)){
            return json_encode(['success' => false, 'message' => 'Este usuario no puede rechazar en el sistema externo', 'icon' => 'error']);
        }

        $config = \App\Utils\Configuration::getConfigurations();
        $idResource = $request->idResource;
        $dataType = $request->dataType;
        $comment = $request->comment;
        $authorize = 0;
        
        try {
            $body = '{
                "idResource": '.$idResource.',
                "dataType": '.$dataType.',
                "authorize": '.$authorize.',
                "comment": "'.$comment.'",
                "userId": '.\Auth::user()->external_id_n.',
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

            // $lResources = $this->getResources();

            $data = AppLinkUtils::getResources(\Auth::user());

            $lResources = [];
            $message = "";
            if($data->code != 200){
                $message = $data->message;
                \Log::error($message);
                return json_encode(['success' => false, 'message' => $message, 'icon' => 'error']);
            }

            $oData = json_decode($data->data);
            $lResources = folioUtils::formatRequisitionsFolio($oData->lAuthData);
            foreach($lResources as $oRes){
                $oRes->date = dateUtils::formatDate($oRes->date, 'D-m-Y');
            }

        } catch (\Throwable $th) {
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'lResources' => $lResources, 'message' => $result->message, 'icon' => 'success']);
    }

    public function getSteps(Request $request){
        $idResource = $request->resource_id;
        try {
            $config = \App\Utils\Configuration::getConfigurations();
            $body = '{
                "idResource": '.$idResource.',
                "user": "'.\Auth::user()->username.'"
            }';

            $result = AppLinkUtils::requestAppLink($config->AppLinkRouteGetSteps, "POST", \Auth::user(), $body);
            if(!is_null($result)){
                if($result->code != 200){
                    return json_encode(['success' => false, 'message' => $result->message, 'icon' => 'error']);
                }
            }else{
                return json_encode(['success' => false, 'message' => 'No se obtuvo respuesta desde AppLink', 'icon' => 'error']);
            }

            $lSteps = json_decode($result->data);

            foreach($lSteps as $step){
                $step->timeAuthorized = dateUtils::formatDate(str_replace("'", "", $step->timeAuthorized), 'D-m-Y mm:HH:ss');
                $step->timeRejected = dateUtils::formatDate(str_replace("'", "", $step->timeRejected), 'D-m-Y mm:HH:ss');
            }

        } catch (\Throwable $th) {
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'lSteps' => $lSteps, 'message' => $result->message, 'icon' => 'success']);
    }

    public function getRows(Request $request){
        try {
            $idResource = $request->idResource;

            $config = \App\Utils\Configuration::getConfigurations();
            $body = '{
                "idResource": '.$idResource.',
                "user": "'.\Auth::user()->username.'"
            }';

            $result = AppLinkUtils::requestAppLink($config->AppLinkRouteGetRows, "POST", \Auth::user(), $body);
            if(!is_null($result)){
                if($result->code != 200){
                    return json_encode(['success' => false, 'message' => $result->message, 'icon' => 'error']);
                }
            }else{
                return json_encode(['success' => false, 'message' => 'No se obtuvo respuesta desde AppLink', 'icon' => 'error']);
            }

            $lRows = json_decode($result->data);
            foreach($lRows as $row){
                $row->qty = formatersUtils::formatNumber($row->qty);
                $row->priceUnit = formatersUtils::formatCoin($row->priceUnit);
                $row->total = formatersUtils::formatCoin($row->total);
            }
        } catch (\Throwable $th) {
            \Log::error($th);
            return json_encode(['success' => false, 'message' => $th->getMessage(), 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'lRows' => $lRows]);
    }
}