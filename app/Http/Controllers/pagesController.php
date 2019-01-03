<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class pagesController extends Controller
{
    function index()
    {
        return view('index');
    }

    function runRuby()
    {
        $usrPas = Auth::user()->igUsername . " " . Auth::user()->igPassword;
        $cmd = "ruby instagram.rb " . $usrPas;
        $output = system($cmd);
        return redirect()->route('index');
    }
}
