<?php


use App\Http\Controllers\Shop\AuthController;
use App\Http\Controllers\Shop\ConfigController;
use App\Http\Controllers\Shop\FeedbackController;
use App\Http\Controllers\Shop\HistoryMatchController;
use App\Http\Controllers\Shop\OrderController;
use App\Http\Controllers\Shop\ShopController;
use App\Http\Controllers\Shop\ShopLinkController;
use App\Http\Controllers\Shop\VersionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    //登录
    Route::post('login', [AuthController::class, 'login']);

    Route::get('version', [VersionController::class, 'show']);

    Route::post('link', [ShopLinkController::class, 'store']);

    Route::middleware('checkShop')->group(function () {

        Route::get('info', [ShopController::class, 'info']);
        Route::put('info', [ShopController::class, 'update']);

        Route::post('order', [OrderController::class, 'store']);
        Route::get('order/statistical', [OrderController::class, 'statistical']);
        Route::put('order/{id}', [OrderController::class, 'update']);
        Route::get('order', [OrderController::class, 'index']);
        Route::get('order/{id}', [OrderController::class, 'show']);

        Route::get('feedback/read', [FeedbackController::class, 'is_read']);
        Route::resource('feedback', FeedbackController::class)->only('index', 'store', 'show');

        //历史数据
        Route::get('history/match/{path}', [HistoryMatchController::class, 'show']);
        Route::get('history/day', [HistoryMatchController::class, 'day']);

        //配置
        Route::get('config/{key}', [ConfigController::class, 'info']);
    });

});
