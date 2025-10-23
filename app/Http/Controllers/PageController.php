<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Show the application's landing page.
     */
    public function home()
    {
        return view('welcome');
    }

    /**
     * Show the login page.
     */
    public function login()
    {
        return view('auth.login');
    }

    /**
     * Show the registration page.
     */
    public function register()
    {
        return view('auth.register');
    }
}