<?php

namespace App\Http\Controllers\Assessment;

use App\Http\Controllers\Controller;
use function view;

class PrivateController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function dashboard(){
        return view('assessment.private.dashboard')->with('title','Dashboard');
    }
}
