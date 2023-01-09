<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;

class TestController extends Controller
{
    public function index(Test $test){
        return view('tests/index')->with(['tests' => $test->get()]);
    }
}
