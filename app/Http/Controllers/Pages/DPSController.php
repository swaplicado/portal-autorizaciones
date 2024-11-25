<?php

namespace App\Http\Controllers\Pages;

use App\Dps\DpsCore;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DPSController extends Controller
{
    public function index()
    {
        // // leer archivo json
        // $json = file_get_contents(base_path('ocs.json'));
        // // convertir a array
        // $data = json_decode($json, true);
        // $lDocuments = $data['lDocuments'];
        
        return view('dps.index');
    }

    public function getDocumentsInRange(Request $request)
    {
        $firstDay = $request->input('firstDay');
        $lastDay = $request->input('lastDay');
        $idUser = 11;
        $lDocs = DpsCore::getDocuments($firstDay, $lastDay, $idUser, \Auth::user());
        return response()->json($lDocs);
    }

    public function getDocument($idYear, $idDoc)
    {
        $lDocs = DpsCore::getDocument($idYear, $idDoc, \Auth::user());
        return response()->json($lDocs);
    }

    public function view(Request $request, $idYear = 0, $idDoc = 0) {
        return view('dps.view')->with('idYear', $idYear)
                                    ->with('idDoc', $idDoc);
    }
}
