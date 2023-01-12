<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CalenderController; // 追加忘れずに

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// FullCalender表示用（月）
Route::get('/calender', [CalenderController::class, 'calender'])->name("calender");
