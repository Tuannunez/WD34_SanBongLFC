<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\News;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::orderByDesc('published_at')->paginate(15);

        return view('admin.news.index', compact('news'));
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string',
            'content' => 'nullable|string',
            'image' => 'nullable|string',
            'image_file' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:5120',
            'is_published' => 'nullable|in:0,1,true,false',
            'published_at' => 'nullable|date',
        ]);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('news', 'public');
            $data['image'] = Storage::url($path);
        }

        // remove temporary upload key before saving to DB
        if (array_key_exists('image_file', $data)) {
            unset($data['image_file']);
        }

        $data['is_published'] = $request->boolean('is_published');
        if ($data['is_published'] && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $data['slug'] = Str::slug($data['title']) ?: Str::random(8);
        $news = News::create($data);

        return redirect()->route('admin.news.index')->with('success', 'Bài viết đã được tạo.');
    }

    public function edit(News $news)
    {
        return view('admin.news.edit', compact('news'));
    }

    public function update(Request $request, News $news)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string',
            'content' => 'nullable|string',
            'image' => 'nullable|string',
            'image_file' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:5120',
            'is_published' => 'nullable|in:0,1,true,false',
            'published_at' => 'nullable|date',
        ]);

        if ($request->hasFile('image_file')) {
            // delete old image file if exists and stored in storage
            if (! empty($news->image) && str_starts_with($news->image, '/storage/')) {
                $oldPath = ltrim(str_replace('/storage/', '', $news->image), '/');
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('image_file')->store('news', 'public');
            $data['image'] = Storage::url($path);
        }

        // remove temporary upload key before updating DB
        if (array_key_exists('image_file', $data)) {
            unset($data['image_file']);
        }

        $data['is_published'] = $request->boolean('is_published');
        if ($data['is_published'] && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $data['slug'] = Str::slug($data['title']) ?: $news->slug;
        $news->update($data);

        return redirect()->route('admin.news.index')->with('success', 'Bài viết đã được cập nhật.');
    }

    public function destroy(News $news)
    {
        $news->delete();

        return redirect()->route('admin.news.index')->with('success', 'Bài viết đã được xóa.');
    }
}
