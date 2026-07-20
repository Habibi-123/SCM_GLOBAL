<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PortResource;
use App\Models\Port;
use Illuminate\Http\Request;

class PortApiController extends Controller
{
    // GET /api/ports
    public function index(Request $request)
    {
        $ports = Port::query()
            ->with('country:id,code,name')
            ->when($request->filled('search'), fn ($q) =>
                $q->where('name', 'like', '%' . $request->input('search') . '%'))
            ->when($request->filled('country'), fn ($q) =>
                $q->whereHas('country', fn ($cq) => $cq->where('code', $request->input('country'))))
            ->paginate($request->input('per_page', 25));

        return PortResource::collection($ports);
    }
}