<?php

namespace App\Http\Controllers;

use App\Models\Calendar; // Model追加忘れずに
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function calendar(){
        return view("calendars/calendar");
    }
}
