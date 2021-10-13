<?php

use App\Http\Controllers\NewsController;
use App\Models\News;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/news', [NewsController::class, 'getList'])->name('news_list');
Route::get('/{slug}', [NewsController::class, 'getDetails'])->name('news_item');
