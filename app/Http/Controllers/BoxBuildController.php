<?php

namespace App\Http\Controllers;

class BoxBuildController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function boxBuildMenu()
    {
        return view("boxBuild.boxBuildMenu");
    }
}
