<?php

use App\Http\Controllers\SyncResultController;
use App\Http\Controllers\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/sync/wallet', [WalletController::class, 'index']);
Route::post('/sync/jc', [SyncResultController::class, 'jc']);
Route::post('/sync/pls', [SyncResultController::class, 'pls']);
Route::post('/sync/bjdc', [SyncResultController::class, 'bjdc']);
