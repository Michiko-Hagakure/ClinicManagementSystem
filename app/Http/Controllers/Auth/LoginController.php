<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function create() {

        return view('auth.login');
    }

    public function store() {
        //TODO:

        return redirect('/dashboard');
    }
}
