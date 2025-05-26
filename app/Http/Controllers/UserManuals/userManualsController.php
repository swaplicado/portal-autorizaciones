<?php

namespace App\Http\Controllers\UserManuals;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class userManualsController extends Controller
{
    public function index(){
        return view('manuales.manuales');
    }
}