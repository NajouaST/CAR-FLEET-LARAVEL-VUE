<?php

namespace App\Http\Controllers\API\params;

use App\Http\Controllers\Controller;
use App\Http\Resources\NameResource;
use App\Models\FamilleVehicule;
use App\Traits\HasQueryFilters;
use Illuminate\Http\Request;

// Assuming you want to notify users
class FamilleVehiculeController extends Controller
{
    use HasQueryFilters;

    public function index(Request $request)
    {
        $query = FamilleVehicule::query();

        $result = $this->applyQueryParameters($query, $request);

        return response()->json($result);
    }

    public function listNames()
    {
        $famille = FamilleVehicule::all();

        return NameResource::collection($famille);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:type_compteurs,name',
            'renouvelable' => 'required',
        ]);

        $famille = FamilleVehicule::create($validated);

        return response()->json($famille, 201);
    }

    public function show(FamilleVehicule $famille)
    {
        return response()->json($famille);
    }

    public function update(Request $request, FamilleVehicule $famille)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:type_compteurs,name,' . $famille->id,
            'renouvelable' => 'required',
        ]);

        $famille->update($validated);

        return response()->json($famille);
    }

    public function destroy(FamilleVehicule $famille)
    {
        $famille->delete();

        return response()->json(null, 204);
    }
}
