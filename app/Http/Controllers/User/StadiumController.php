<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Stadium;
use Illuminate\Http\Request;

class StadiumController extends Controller
{
    // Trang chủ
    public function index(Request $request)
    {
        $keyword = $request->keyword;

        $stadiums = Stadium::query()
            ->when($keyword, function ($query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%");
            })
            ->latest()
            ->get();

        return view('user.stadiums.index', compact('stadiums'));
    }

    // // Chi tiết sân
    // public function show($id)
    // {
    //     $stadium = Stadium::findOrFail($id);

    //     return view('user.stadiums.show', compact('stadium'));
    // }

    
    
}