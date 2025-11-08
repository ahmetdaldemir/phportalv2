<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VueController extends Controller
{
    /**
     * Show the Vue application
     */
    public function index()
    {
        return view('vue.app');
    }

    /**
     * Handle all Vue routes (SPA)
     */
    public function catchAll()
    {
        return view('vue.app');
    }
}
