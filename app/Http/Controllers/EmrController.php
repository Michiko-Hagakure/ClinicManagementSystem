<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmrController extends Controller
{
    public function index() {
        // TODO:

        return view('emr.index');
    }

    public function show($patient) {
        // TODO:

        return view('emrshow');
    }
}
