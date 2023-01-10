<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WeatherController; // 追加忘れずに

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

// 気象庁の天気予報WebAPI経由で表示(東京都の概要)
Route::get('/weathers', [WeatherController::class, 'index']);
