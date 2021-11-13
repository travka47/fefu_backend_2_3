<?php

use App\Http\Controllers\AppealController;
use App\Http\Controllers\NewsController;
use App\Http\Middleware\SuggestAppeal;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/news', [NewsController::class, 'getList'])->name('news_list');
Route::get('/news/{slug}', [NewsController::class, 'getDetails'])->name('news_item');
Route::match(['get', 'post'], '/appeal', AppealController::class)->name('appeal')->withoutMiddleware([SuggestAppeal::class]);
