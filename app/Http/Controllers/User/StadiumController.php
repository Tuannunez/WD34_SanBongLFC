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

    public function show($id)
    {
        $stadium = Stadium::findOrFail($id);

        $timeSlots = [
            [
                'session' => 'Buổi sáng',
                'slots' => [
                    ['time' => '06:00 - 08:00', 'price' => 160000],
                    ['time' => '08:00 - 10:00', 'price' => 160000],
                    ['time' => '10:00 - 12:00', 'price' => 140000],
                ]
            ],
            [
                'session' => 'Buổi chiều',
                'slots' => [
                    ['time' => '14:00 - 16:00', 'price' => 180000],
                    ['time' => '16:00 - 18:00', 'price' => 200000],
                ]
            ],
            [
                'session' => 'Buổi tối',
                'slots' => [
                    ['time' => '18:00 - 19:30', 'price' => 240000],
                    ['time' => '19:30 - 21:00', 'price' => 260000],
                    ['time' => '21:00 - 22:30', 'price' => 200000],
                ]
            ]
        ];

        return view(
            'user.stadiums.show',
            compact('stadium', 'timeSlots')
        );
    }
}