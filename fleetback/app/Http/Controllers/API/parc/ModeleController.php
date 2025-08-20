<?php

namespace App\Http\Controllers\API\parc;

use App\Http\Controllers\Controller;
use App\Http\Resources\NameResource;
use App\Models\Modele;
use App\Traits\HasQueryFilters;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModeleController extends Controller
{
    use HasQueryFilters;

    public function listNames()
    {
        $modeles = Modele::select('id', 'name')->get();
        return NameResource::collection($modeles);
    }

    public function index(Request $request)
    {
        $query = Modele::with(['marque', 'gamme', 'typeCompteur', 'typeCarburant']);

        $result = $this->applyQueryParameters($query, $request);
        return response()->json($result);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'CO2'               => 'nullable|numeric',
            'Cylindre'          => 'nullable|numeric',
            'Poids'             => 'nullable|numeric',
            'marque_id'         => 'required|exists:marques,id',
            'gamme_id'          => 'nullable|exists:gammes,id',
            'type_compteur_id'  => 'nullable|exists:type_compteurs,id',
            'type_carburant_id' => 'nullable|exists:type_carburants,id',
            'image'             => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('modeles', 'public');
        }

        $modele = Modele::create($data);

        return response()->json($modele->load(['marque', 'gamme', 'typeCompteur', 'typeCarburant']), 201);
    }

    public function show(Modele $modele)
    {
        return $modele->load(['marque', 'gamme', 'typeCompteur', 'typeCarburant']);
    }

    public function update(Request $request, Modele $modele)
    {
        $data = $request->validate([
            'name'              => 'sometimes|required|string|max:255',
            'CO2'               => 'nullable|numeric',
            'Cylindre'          => 'nullable|numeric',
            'Poids'             => 'nullable|numeric',
            'marque_id'         => 'sometimes|required|exists:marques,id',
            'gamme_id'          => 'nullable|exists:gammes,id',
            'type_compteur_id'  => 'nullable|exists:type_compteurs,id',
            'type_carburant_id' => 'nullable|exists:type_carburants,id',
            'image'             => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($modele->image) {
                Storage::disk('public')->delete($modele->image);
            }
            $data['image'] = $request->file('image')->store('modeles', 'public');
        }

        $modele->update($data);

        return response()->json($modele->load(['marque', 'gamme', 'typeCompteur', 'typeCarburant']));
    }

    public function destroy(Modele $modele)
    {
        if ($modele->image) {
            Storage::disk('public')->delete($modele->image);
        }
        $modele->delete();
        return response()->noContent();
    }
}
