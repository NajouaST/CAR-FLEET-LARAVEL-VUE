<?php

namespace App\Http\Controllers\API\params;

use App\Http\Controllers\Controller;
use App\Http\Resources\NameResource;
use App\Models\Zone;
use App\Traits\HasQueryFilters;
use Illuminate\Http\Request;

class ZoneController extends Controller
{
    use HasQueryFilters;

    public function index(Request $request)
    {
        $query = Zone::query();
        $result = $this->applyQueryParameters($query, $request);
        return response()->json($result);
    }

    public function listNames()
    {
        // $zones = Zone::all();
        // return NameResource::collection($zones);
        return Zone::select('id','nom')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:zones,nom',
        ]);

        $zone = Zone::create($validated);
        return response()->json($zone, 201);
    }

    public function show(Zone $zone)
    {
        return response()->json($zone);
    }

    public function update(Request $request, Zone $zone)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:zones,nom,' . $zone->id,
        ]);

        $zone->update($validated);
        return response()->json($zone);
    }

    public function destroy(Zone $zone)
    {
        $zone->delete();
        return response()->json(null, 204);
    }
}