<?php

namespace App\Http\Controllers;

use App\Models\Calender; // Model追加忘れずに
use Illuminate\Http\Request;

class CalenderController extends Controller
{
    public function calender(){
        return view("calenders/calender");
    }
}
