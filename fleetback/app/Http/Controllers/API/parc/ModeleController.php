<?php

namespace App\Http\Controllers\API\parc;

use App\Http\Controllers\Controller;
use App\Http\Resources\ModeleResource;
use App\Http\Resources\NameResource;
use App\Models\Modele;
use App\Traits\HasQueryFilters;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModeleController extends Controller
{
    use HasQueryFilters;
    public function listNames(Request $request)
    {
        $query = Modele::with(['marque', 'gamme', 'typeCompteur', 'typeCarburant']);

        $result = $this->applyQueryParameters($query, $request);
        return collect($result['data'])
            ->map(fn($item) => (new ModeleResource($item))->additional(['mode' => 'grid']));
    }

    // -----------------------
    // Index with filters
    // -----------------------
    public function index(Request $request)
    {
        $query = Modele::with(['marque', 'gamme', 'typeCompteur', 'typeCarburant']);

        $result = $this->applyQueryParameters($query, $request);

        return [
            'code' => 200,
            'message' => 'Modeles retrieved',
            'data' => collect($result['data'])
                ->map(fn($item) => (new ModeleResource($item))->additional(['mode' => 'grid'])),
            'totalRecords' => $result['totalRecords'],
        ];
    }

    // -----------------------
    // Store
    // -----------------------
    public function store(Request $request)
    {
        $data = $this->validateRequest($request);

        $modele = Modele::create($data);

        return [
            'code' => 201,
            'message' => 'Modele created successfully',
            'data' => new ModeleResource($modele->load(['marque', 'gamme', 'typeCompteur', 'typeCarburant'])),
        ];
    }

    // -----------------------
    // Show
    // -----------------------
    public function show(Modele $modele)
    {
        $modele->load(['marque', 'gamme', 'typeCompteur', 'typeCarburant']);
        return new ModeleResource($modele);
    }

    // -----------------------
    // Update
    // -----------------------
    public function update(Request $request, Modele $modele)
    {
        $data = $this->validateRequest($request, $modele->id);

        $modele->update($data);

        return [
            'code' => 200,
            'message' => 'Modele updated successfully',
            'data' => new ModeleResource($modele->load(['marque', 'gamme', 'typeCompteur', 'typeCarburant'])),
        ];
    }

    // -----------------------
    // Destroy
    // -----------------------
    public function destroy(Modele $modele)
    {
        $modele->delete();

        return [
            'code' => 200,
            'message' => 'Modele deleted successfully',
        ];
    }

    // -----------------------
    // Helpers
    // -----------------------
    private function validateRequest(Request $request, $modeleId = null)
    {
        return $request->validate([
            'name'              => 'required|string|max:255',
            'puissance_cv'      => 'nullable|numeric',
            'puissance_din'     => 'nullable|numeric',
            'places'            => 'nullable|integer',
            'poids_vide'        => 'nullable|numeric',
            'poids_tc'          => 'nullable|numeric',
            'charge_utile'      => 'nullable|numeric',
            'cylindre'          => 'nullable|numeric',
            'consommation_min'  => 'nullable|numeric',
            'consommation_max'  => 'nullable|numeric',
            'consommation_moy'  => 'nullable|numeric',
            'marque_id'         => 'required|exists:marques,id',
            'gamme_id'          => 'nullable|exists:gammes,id',
            'type_compteur_id'  => 'nullable|exists:type_compteurs,id',
            'type_carburant_id' => 'nullable|exists:type_carburants,id',
        ]);
    }
}
