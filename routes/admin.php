<?php


use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AppVersionController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ConfigController;
use App\Http\Controllers\Admin\FeedbackController;
use App\Http\Controllers\Admin\ShopController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ShopLinkController;
use App\Http\Controllers\Admin\UploadController;
use App\Http\Controllers\Admin\VersionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    //登录
//    Route::post('login', [AuthController::class, 'login']);
//
//    Route::middleware('checkAdmin')->group(function () {
//
//        //管理员
//        Route::post('admin', [AdminUserController::class, 'store']);
//        Route::put('admin/{id}', [AdminUserController::class, 'update']);
//        Route::get('admin', [AdminUserController::class, 'index']);
//        Route::get('admin/{id}', [AdminUserController::class, 'show']);
//
//        //店铺
//        Route::post('shop', [ShopController::class, 'store']);
//        Route::put('shop/{id}', [ShopController::class, 'update']);
//        Route::get('shop', [ShopController::class, 'index']);
//        Route::get('shop/{id}', [ShopController::class, 'show']);
//
//
//        Route::get('order', [OrderController::class, 'index']);
//        Route::get('order/statistical', [OrderController::class, 'statistical']);
//
//        Route::resource('app-version', AppVersionController::class);
//
//        Route::post('upload', [UploadController::class, 'upload']);
//
//        Route::resource('feedback', FeedbackController::class)->only('index', 'update');
//
//        Route::post('version', [VersionController::class, 'store']);
//
//        Route::resource('link', ShopLinkController::class)->only('index', 'destroy');
//
//        // 配置
//        Route::post('config', [ConfigController::class, 'store']);
//        Route::put('config/{id}', [ConfigController::class, 'update']);
//        Route::get('config', [ConfigController::class, 'index']);
//        Route::get('config/{key}', [ConfigController::class, 'show']);
//    });

});
