<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController; // 追加忘れずに

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

Route::get('/calendar', [EventController::class, 'show'])->name("show"); // カレンダー表示
Route::post('/calendar/get',  [EventController::class, 'get'])->name("get"); // event取得（ページ表示ではないのでpost）
Route::post('/calendar/create', [EventController::class, 'create'])->name("create"); // event追加
Route::put('/calendar/update', [EventController::class, 'update'])->name("update"); // event更新
Route::delete('/calendar/delete', [EventController::class, 'delete'])->name("delete"); // event削除
