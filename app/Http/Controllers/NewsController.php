<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller {
    public function getList() {
        $news_list = News::query()->where('is_published', '=', true)->where('published_at', '<=', 'now')->orderByDesc('published_at')->paginate(5);
        return view('news_list', ['news' => $news_list]);
    }

    public function getDetails(string $slug) {
        $news_item = News::where('slug', $slug)->where('published_at', '<=', 'now')->first();
        if ($news_item === null || $news_item->is_published === false || $news_item->published_at >= 'now') {
            abort(404);
        }
        return view('news_item', ['news_item' => $news_item]);
    }
}
