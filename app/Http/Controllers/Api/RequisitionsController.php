<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RequisitionsController extends Controller
{
    public function test(){
        return response()->json(['message' => 'Esto es una prueba'], 200);
    }
}
