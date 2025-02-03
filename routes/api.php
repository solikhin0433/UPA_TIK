<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\LogoutController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\DaftarMenuController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register',RegisterController::class)->name('register');
Route::post('/register1',RegisterController::class)->name('register1');
Route::post('/login',LoginController::class)->name('login');
Route::middleware('auth:api')->get('/user',function(Request $request){
    return $request->user();
});
Route::post('/logout', LogoutController::class)->name('logout');

Route::group(['prefix' => 'menus'], function () {
    Route::get('/', [MenuController::class, 'index']);
    Route::post('/', [MenuController::class, 'store']);
    Route::get('/{id}', [MenuController::class, 'show']);
    Route::put('/{id}', [MenuController::class, 'update']);
    Route::delete('/{id}', [MenuController::class, 'destroy']);
});

Route::group(['prefix' => 'daftarMenu', 'middleware' => 'authorize:ADM'], function () {
    Route::get('/', [DaftarMenuController::class, 'index']);
    Route::get('/{id}/subMenu', [DaftarMenuController::class, 'subMenu']);
    Route::get('/content/{pageId}', [DaftarMenuController::class, 'content']);
});