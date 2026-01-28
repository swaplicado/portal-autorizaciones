<?php

namespace App\Http\Controllers\Pages;

use App\Dps\DpsCore;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Logger\CloudLogger;

/**
 * Class DPSController
 * @package App\Http\Controllers\Pages
 */
class DPSController extends Controller
{
    /**
     * Display the DPS index page.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // return view('dps.index')->with('bUser', 1)
        //                         ->with('statusFilter', 0);
        return view('unavailable');
    }

    /**
     * Display the DPS index page with pending status.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function indexPending(Request $request)
    {
        // return view('dps.index')->with('bUser', 1)
        //                         ->with('statusFilter', -1);
        return view('unavailable');
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
        $bUser = 1;
        $statusFilter = $request->input('statusFilter');
        $idCompany = $request->input('idCompany');
        $idCompany = empty($idCompany) ? 0 : $idCompany;
        if ($bUser > 0) {
            $idUser = \Auth::user()->external_id_n;
        } else {
            $idUser = 0;
        }

        $lDocs = DpsCore::getDocuments($firstDay, $lastDay, $idUser, \Auth::user(), $statusFilter, $idCompany);

        return response()->json($lDocs);
    }

    /**
     * Get a specific document.
     *
     * @param Request $request
     * @param int $idYear
     * @param int $idDoc
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDocument(Request $request, $idYear, $idDoc, $idCompany = 0)
    {
        $lDocs = DpsCore::getDocument($idYear, $idDoc, \Auth::user(), $idCompany);

        return response()->json($lDocs);
    }

    /**
     * Authorize a specific DPS document.
     *
     * @param Request $request
     * @param int $idYear
     * @param int $idDoc
     * @return \Illuminate\Http\JsonResponse
     */
    public function authorizeDps(Request $request, $idYear, $idDoc, $idCompany = 0)
    {
        $sComments = $request->input('comments');
        $jResponse = DpsCore::authorizeDps($idYear, $idDoc, \Auth::user(), $sComments, $idCompany);

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
    public function rejectDps(Request $request, $idYear, $idDoc, $idCompany = 0)
    {
        $sComments = $request->input('comments');
        // Validar sComments, son obligatorios
        if (empty($sComments)) {
            return response()->json(['error' => 'Los comentarios son obligatorios'], 400);
        }
        $oResponse = DpsCore::rejectDps($idYear, $idDoc, \Auth::user(), $sComments, $idCompany);

        return response()->json($oResponse);
    }

    /**
     * Display the DPS view page.
     *
     * @param Request $request
     * @param int $idYear
     * @param int $idDoc
     * @return \Illuminate\View\View
     */
    public function view(Request $request, $idYear = 0, $idDoc = 0)
    {
        return view('dps.view')->with('idYear', $idYear)
                               ->with('idDoc', $idDoc);
    }
}
