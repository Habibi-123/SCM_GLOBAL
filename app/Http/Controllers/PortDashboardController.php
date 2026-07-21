<?php

namespace App\Http\Controllers;

use App\Models\Port;
use Illuminate\Http\Request;

class PortDashboardController extends Controller
{
    public function index()
    {
        $countries = \App\Models\Country::orderBy('name')->get(['code', 'name']);
        return view('ports.index', compact('countries'));
    }

    public function data(Request $request)
    {
        $query = Port::with('country:id,name,code');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        if ($request->filled('country_code')) {
            $query->whereHas('country', fn ($q) => $q->where('code', $request->input('country_code')));
        }

        $ports = $query->limit(500)->get(['id', 'country_id', 'name', 'latitude', 'longitude']);

        return response()->json($ports);
    }
}