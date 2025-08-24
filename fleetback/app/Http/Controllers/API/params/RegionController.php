<?php

namespace App\Http\Controllers\API\params;

use App\Http\Controllers\Controller;
use App\Http\Resources\NameResource;
use App\Models\Region;
use App\Traits\HasQueryFilters;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    use HasQueryFilters;

    public function index(Request $request)
    {
        $query = Region::query();
        $result = $this->applyQueryParameters($query, $request);
        return response()->json($result);
    }

    public function listNames()
    {
        // $regions = Region::all();
        // return NameResource::collection($regions);
        return Region::select('id','nom')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:regions,nom',
        ]);

        $region = Region::create($validated);
        return response()->json($region, 201);
    }

    public function show(Region $region)
    {
        return response()->json($region);
    }

    public function update(Request $request, Region $region)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:regions,nom,' . $region->id,
        ]);

        $region->update($validated);
        return response()->json($region);
    }

    public function destroy(Region $region)
    {
        $region->delete();
        return response()->json(null, 204);
    }
}