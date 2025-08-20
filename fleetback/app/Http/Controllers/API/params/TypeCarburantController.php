<?php

namespace App\Http\Controllers\API\params;

use App\Http\Controllers\Controller;
use App\Http\Resources\NameResource;
use App\Models\TypeCarburant;
use App\Traits\HasQueryFilters;
use Illuminate\Http\Request;

// Assuming you want to notify users
class TypeCarburantController extends Controller
{
    use HasQueryFilters;

    public function index(Request $request)
    {
        $query = TypeCarburant::query();

        $result = $this->applyQueryParameters($query, $request);

        return response()->json($result);
    }

    public function listNames()
    {
        $gamme = TypeCarburant::all();

        return NameResource::collection($gamme);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:type_carburants,name',
        ]);

        $carburant = TypeCarburant::create($validated);

        return response()->json($carburant, 201);
    }

    public function show(TypeCarburant $typeCarburant)
    {
        return response()->json($typeCarburant);
    }

    public function update(Request $request, TypeCarburant $typeCarburant)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:type_carburants,name,' . $typeCarburant->id,
        ]);

        $typeCarburant->update($validated);

        return response()->json($typeCarburant);
    }

    public function destroy(TypeCarburant $typeCarburant)
    {
        $typeCarburant->delete();

        return response()->json(null, 204);
    }
}
