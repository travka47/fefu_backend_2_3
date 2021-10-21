<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller {
    public function getList() {
        $news_list = News::query()
            ->where('is_published',true)
            ->where('published_at', '<=', 'NOW()')
            ->orderByRaw('published_at DESC, id DESC')
            ->paginate(5);
        return view('news_list', ['news' => $news_list]);
    }

    public function getDetails(string $slug) {
        $news_item = News::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->where('published_at', '<=', 'NOW()')
            ->first();
        if ($news_item === null) {
            abort(404);
        }
        return view('news_item', ['news_item' => $news_item]);
    }
}
