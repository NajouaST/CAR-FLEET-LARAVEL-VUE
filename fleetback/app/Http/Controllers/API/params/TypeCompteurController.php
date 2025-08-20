<?php

namespace App\Http\Controllers\API\params;

use App\Http\Controllers\Controller;
use App\Http\Resources\NameResource;
use App\Models\TypeCompteur;
use App\Traits\HasQueryFilters;
use Illuminate\Http\Request;

// Assuming you want to notify users
class TypeCompteurController extends Controller
{
    use HasQueryFilters;

    public function index(Request $request)
    {
        $query = TypeCompteur::query();

        $result = $this->applyQueryParameters($query, $request);

        return response()->json($result);
    }

    public function listNames()
    {
        $gamme = TypeCompteur::all();

        return NameResource::collection($gamme);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:type_compteurs,name',
        ]);

        $compteur = TypeCompteur::create($validated);

        return response()->json($compteur, 201);
    }

    public function show(TypeCompteur $typeCompteur)
    {
        return response()->json($typeCompteur);
    }

    public function update(Request $request, TypeCompteur $typeCompteur)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:type_compteurs,name,' . $typeCompteur->id,
        ]);

        $typeCompteur->update($validated);

        return response()->json($typeCompteur);
    }

    public function destroy(TypeCompteur $typeCompteur)
    {
        $typeCompteur->delete();

        return response()->json(null, 204);
    }
}
