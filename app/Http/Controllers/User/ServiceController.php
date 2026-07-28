<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::query()
            ->where('status', true)
            ->orderBy('name')
            ->get();

        return view('user.services.index', compact('services'));
    }

    public function show(Service $service)
    {
        abort_if(! $service->status, 404);

        return view('user.services.show', compact('service'));
    }
}
