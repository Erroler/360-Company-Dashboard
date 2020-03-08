<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JasminService;

class SearchController extends Controller
{
    protected $service;

    public function inventory(Request $request)
    {
        return response()->json($this->service->inventory($request->query()));
    }

    public function purchases(Request $request)
    {
        return response()->json($this->service->purchases($request->query()));
    }

    public function sales(Request $request)
    {
        return response()->json($this->service->sales($request->query()));
    }

    public function __construct(JasminService $service)
    {
        $this->service = $service;
    }
}
