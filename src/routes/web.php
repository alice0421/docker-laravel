<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

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

// 1つのルートにmiddleware適用
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware(['auth', 'verified'])->group(function () {
//     Route::get('/dashboard', function () { return view('dashboard'); })->name('dashboard');
// });

// 複数のルートにmiddleware適用
Route::middleware('auth')->group(function () {
    // Route::get('/dashboard', function () { return view('dashboard'); })->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// コントローラーごとにmiddleware適用
Route::controller(TestController::class)->middleware(['auth'])->group(function(){
    Route::get('/test', 'index')->name('test.index');
});

require __DIR__.'/auth.php';
