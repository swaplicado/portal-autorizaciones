<?php

namespace App\Http\Controllers\Pages;

use Log;
use App\Dps\DpsCore;
use App\Rm\RmCore;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\Core;

/**
 * Class DPSController
 * @package App\Http\Controllers\Pages
 */
class RMController extends Controller
{
    /**
     * Display the RM index page.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        //RmCore::getDocuments('2025-04-01', '2025-04-30', 95, \Auth::user());
        return view('rm.index')->with('bUser', 1)
                                 ->with('statusFilter', 0);
    }

    /**
     * Display the RM index page with pending status.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function indexPending(Request $request)
    {
        return view('rm.index')->with('bUser', 1)
                                ->with('statusFilter', -1);
    }

    public function indexMyRm(Request $request){
        return view('rm.index')->with('bUser', 1)
                                ->with('statusFilter', -2);
    }

    /**
     * Get documents in a specified date range.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDocumentsInRange(Request $request)
    {
        $firstDay = $request->input('firstDay');
        $lastDay = $request->input('lastDay');
        $bUser = $request->input('bUser');
        $statusFilter = $request->input('statusFilter');
        if ($bUser > 0) {
            $idUser = \Auth::user()->external_id_n;
        } else {
            $idUser = 0;
        }
        Log::info($firstDay);
        //dd($firstDay);
        $lDocs = RmCore::getDocuments($firstDay, $lastDay, $idUser, \Auth::user(), $statusFilter);
        return response()->json($lDocs);
    }

    public function getDocument(Request $request, $idMaterialRequest)
    {
        //$idMaterialRequest = 8447;
        $lDocs = RmCore::getDocument($idMaterialRequest, \Auth::user());
        return response()->json($lDocs);
    }

    public function getMyDocument(Request $request){
        $firstDay = $request->input('firstDay');
        $lastDay = $request->input('lastDay');
        $bUser = $request->input('bUser');
        $statusFilter = $request->input('statusFilter');
        if ($bUser > 0) {
            $idUser = \Auth::user()->external_id_n;
        } else {
            $idUser = 0;
        }
        Log::info($firstDay);
        //dd($firstDay);
        $lDocs = RmCore::getDocuments($firstDay, $lastDay, $idUser, \Auth::user(), $statusFilter);
        return response()->json($lDocs);    
    }

    /**
     * Display the DPS view page.
     *
     * @param Request $request
     * @param int $idYear
     * @param int $idDoc
     * @return \Illuminate\View\View
     */
    public function view(Request $request, $idMaterialRequest = 0)
    {
        return view('rm.view')->with('idMaterialRequest', $idMaterialRequest);
    }

    /**
     * Authorize a specific DPS document.
     *
     * @param Request $request
     * @param int $idYear
     * @param int $idDoc
     * @return \Illuminate\Http\JsonResponse
     */
    public function authorizeRm(Request $request, $idDoc)
    {
        $sComments = $request->input('comments');
        $jResponse = RmCore::authorizeRm($idDoc, \Auth::user(), $sComments);
        return response()->json($jResponse);
    }

    /**
     * Reject a specific DPS document.
     *
     * @param Request $request
     * @param int $idYear
     * @param int $idDoc
     * @return \Illuminate\Http\JsonResponse
     */
    public function rejectRm(Request $request, $idDoc)
    {
        $sComments = $request->input('comments');
        // Validar sComments, son obligatorios
        if (empty($sComments)) {
            return response()->json(['error' => 'Los comentarios son obligatorios'], 400);
        }
        $oResponse = RmCore::rejectRm($idDoc, \Auth::user(), $sComments);
        return response()->json($oResponse);
    }
}