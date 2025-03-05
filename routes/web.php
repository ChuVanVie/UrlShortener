<?php

use App\Http\Controllers\UrlController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });


//Url Shortener endpoints
Route::get('/', [UrlController::class, 'index'])->name('home');
Route::post('/shorten', [UrlController::class, 'store'])->name('shorten');
Route::get('/shortened/{shortCode}', [UrlController::class, 'show'])->name('shortened');
Route::get('/{shortCode}', [UrlController::class, 'redirect'])->middleware('throttle:10,1');;
