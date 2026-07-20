<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Port;
use App\Models\Country;
use Illuminate\Http\Request;

class PortManagementController extends Controller
{
    public function index(Request $request)
    {
        $ports = Port::with('country:id,name,code')
            ->when($request->filled('search'), fn ($q) =>
                $q->where('name', 'like', '%' . $request->input('search') . '%'))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.ports.index', compact('ports'));
    }

    public function create()
    {
        $countries = Country::orderBy('name')->get(['id', 'name']);
        return view('admin.ports.create', compact('countries'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'name' => 'required|string|max:255',
            'unlocode' => 'nullable|string|max:10',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        Port::create($validated);

        return redirect()->route('admin.ports.index')->with('success', 'Pelabuhan berhasil ditambahkan.');
    }

    public function edit(Port $port)
    {
        $countries = Country::orderBy('name')->get(['id', 'name']);
        return view('admin.ports.edit', compact('port', 'countries'));
    }

    public function update(Request $request, Port $port)
    {
        $validated = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'name' => 'required|string|max:255',
            'unlocode' => 'nullable|string|max:10',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $port->update($validated);

        return redirect()->route('admin.ports.index')->with('success', 'Pelabuhan berhasil diperbarui.');
    }

    public function destroy(Port $port)
    {
        $port->delete();
        return redirect()->route('admin.ports.index')->with('success', 'Pelabuhan berhasil dihapus.');
    }
}