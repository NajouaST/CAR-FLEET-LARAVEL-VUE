<?php

namespace App\Http\Controllers\API\params;

use App\Http\Controllers\Controller;
use App\Http\Resources\NameResource;
use App\Models\FamilleVehicule;
use App\Models\Fournisseur;
use App\Traits\HasQueryFilters;
use Illuminate\Http\Request;

// Assuming you want to notify users
class FournisseurController extends Controller
{
    use HasQueryFilters;

    public function index(Request $request)
    {
        $query = Fournisseur::query();

        $result = $this->applyQueryParameters($query, $request);

        return response()->json($result);
    }

    public function listNames()
    {
        $fournisseur = Fournisseur::all();

        return NameResource::collection($fournisseur);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:type_compteurs,name',
            'email' => 'string',
            'tel' => 'string',
            'adresse' => 'string',
        ]);

        $fournisseur = Fournisseur::create($validated);

        return response()->json($fournisseur, 201);
    }

    public function show(Fournisseur $fournisseur)
    {
        return response()->json($fournisseur);
    }

    public function update(Request $request, Fournisseur $fournisseur)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:type_compteurs,name,' . $fournisseur->id,
            'email' => 'string',
            'tel' => 'string',
            'adresse' => 'string',
        ]);

        $fournisseur->update($validated);

        return response()->json($fournisseur);
    }

    public function destroy(Fournisseur $fournisseur)
    {
        $fournisseur->delete();

        return response()->json(null, 204);
    }
}
