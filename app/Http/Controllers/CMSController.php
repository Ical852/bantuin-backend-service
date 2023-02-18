<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CMSController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('secret');
        $this->middleware('verified');
    }

    public function index()
    {
        return view('cms.pages.index');
    }

    public function helper()
    {
        return view('cms.pages.helper.index');
    }

    public function bantuin()
    {
        return view('cms.pages.bantuin.index');
    }
}
