<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Port;
use Illuminate\Http\Request;

class PortDashboardController extends Controller
{
    public function index()
    {
        $countries = Country::orderBy('name')
            ->get(['code', 'name']);

        return view('ports.index', compact('countries'));
    }

    public function data(Request $request)
    {
        $query = Port::with('country:id,name,code');

        // Filter opsional: cari berdasarkan nama pelabuhan
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        // Filter opsional: berdasarkan negara
        if ($request->filled('country_code')) {
            $query->whereHas('country', fn ($q) => $q->where('code', $request->input('country_code')));
        }

        // Batasi jumlah data yang dikirim ke peta (supaya browser tidak berat)
        $ports = $query->limit(500)->get([
            'id',
            'country_id',
            'name',
            'latitude',
            'longitude'
        ]);

        return response()->json($ports);
    }
}