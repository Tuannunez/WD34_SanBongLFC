<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\News;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::where('is_published', true)
            ->orderByDesc('published_at')
            ->paginate(10);

        return view('user.news.index', compact('news'));
    }

    public function show(News $news)
    {
        if (! $news->is_published) {
            abort(404);
        }

        return view('user.news.show', compact('news'));
    }
}
