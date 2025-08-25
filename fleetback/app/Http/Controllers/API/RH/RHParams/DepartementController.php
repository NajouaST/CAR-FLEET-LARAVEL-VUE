<?php

namespace App\Http\Controllers\API\RH\RHParams;

use App\Http\Controllers\Controller;
use App\Models\Departement;
use App\Traits\HasQueryFilters;
use Illuminate\Http\Request;

class DepartementController extends Controller
{
    use HasQueryFilters;

    public function index(Request $request)
    {
        $query = Departement::query();
        $result = $this->applyQueryParameters($query, $request);
        return response()->json($result);
    }

    public function listNames()
    {
        // $departements = Departement::all();
        // return NameResource::collection($departements);
        return Departement::select('id','nom')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:departements,nom',
        ]);

        $departement = Departement::create($validated);
        return response()->json($departement, 201);
    }

    public function show(Departement $departement)
    {
        return response()->json($departement);
    }

    public function update(Request $request, Departement $departement)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:departements,nom,' . $departement->id,
        ]);

        $departement->update($validated);
        return response()->json($departement);
    }

    public function destroy(Departement $departement)
    {
        $departement->delete();
        return response()->json(null, 204);
    }
}
