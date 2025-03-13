<?php

namespace App\Http\Controllers\Pages;

use Log;
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
        return view('dps.index')->with('bUser', 0)
                                ->with('statusFilter', 0);
    }

    /**
     * Display the DPS index page with pending status.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function indexPending(Request $request)
    {
        return view('dps.index')->with('bUser', 1)
                                ->with('statusFilter', -1);
    }

    /**
     * Get documents in a specified date range.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDocumentsInRange(Request $request)
    {
        // try {
            $firstDay = $request->input('firstDay');
            $lastDay = $request->input('lastDay');
            $bUser = $request->input('bUser');
            $statusFilter = $request->input('statusFilter');
            if ($bUser > 0) {
                $idUser = \Auth::user()->external_id_n;
            } else {
                $idUser = 0;
            }
            $lDocs = DpsCore::getDocuments($firstDay, $lastDay, $idUser, \Auth::user(), $statusFilter);

            return response()->json($lDocs);
        // }
        // catch (\Throwable $th) {
        //     CloudLogger::log('error', $th->getMessage());
        //     Log::error($th);

        //     return response()->json(['error' => $th->getMessage()], 500);
        // }
    }

    /**
     * Get a specific document.
     *
     * @param Request $request
     * @param int $idYear
     * @param int $idDoc
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDocument(Request $request, $idYear, $idDoc)
    {
        try {
            $lDocs = DpsCore::getDocument($idYear, $idDoc, \Auth::user());

            return response()->json($lDocs);
        }
        catch (\Throwable $th) {
            CloudLogger::log('error', $th->getMessage());
            Log::error($th);

            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    /**
     * Authorize a specific DPS document.
     *
     * @param Request $request
     * @param int $idYear
     * @param int $idDoc
     * @return \Illuminate\Http\JsonResponse
     */
    public function authorizeDps(Request $request, $idYear, $idDoc)
    {
        // try {
            $sComments = $request->input('comments');
            $jResponse = DpsCore::authorizeDps($idYear, $idDoc, \Auth::user(), $sComments);

            // CloudLogger::log('info', sprintf(
            //     "DPS autorizado [%d, %d] por %s [external_id_n: %s]%s",
            //     $idYear,
            //     $idDoc,
            //     \Auth::user()->username,
            //     \Auth::user()->external_id_n,
            //     !empty($sComments) ? " Comentarios: {$sComments}" : ""
            // ));
            return response()->json($jResponse);
        // }
        // catch (\Throwable $th) {
        //     CloudLogger::log('error', $th->getMessage());
        //     Log::error($th);

        //     return response()->json(['error' => $th->getMessage()], 500);
        // }
    }

    /**
     * Reject a specific DPS document.
     *
     * @param Request $request
     * @param int $idYear
     * @param int $idDoc
     * @return \Illuminate\Http\JsonResponse
     */
    public function rejectDps(Request $request, $idYear, $idDoc)
    {
        // try {
            $sComments = $request->input('comments');
            // Validar sComments, son obligatorios
            if (empty($sComments)) {
                return response()->json(['error' => 'Los comentarios son obligatorios'], 400);
            }
            $oResponse = DpsCore::rejectDps($idYear, $idDoc, \Auth::user(), $sComments);

            // CloudLogger::log('info', sprintf(
            //     "DPS rechazado [%d, %d] por %s [external_id_n: %s]%s",
            //     $idYear,
            //     $idDoc,
            //     \Auth::user()->username,
            //     \Auth::user()->external_id_n,
            //     !empty($sComments) ? " Comentarios: {$sComments}" : ""
            // ));
            return response()->json($oResponse);
        // }
        // catch (\Throwable $th) {
        //     CloudLogger::log('error', $th->getMessage());
        //     Log::error($th);

        //     return response()->json(['error' => $th->getMessage()], 500);
        // }
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
