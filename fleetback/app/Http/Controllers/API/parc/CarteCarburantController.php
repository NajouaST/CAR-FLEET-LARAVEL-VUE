<?php

namespace App\Http\Controllers\API\parc;

use App\Http\Controllers\Controller;
use App\Models\CarteCarburant;
use App\Traits\HasQueryFilters;
use Illuminate\Http\Request;

class CarteCarburantController extends Controller
{
    use HasQueryFilters;

    public function index(Request $request)
    {
        $query = CarteCarburant::query();
        $result = $this->applyQueryParameters($query, $request);
        return response()->json($result);
    }

    public function listNames()
    {
        return CarteCarburant::select('id','n_carte')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'n_carte'          => 'required|string|max:255|unique:carte_carburants,n_carte',
            'plafond_caburant' => 'required|numeric',
            'unite'            => 'required|string|max:50',
            'plafond_service' => 'required|numeric',
        ]);

        $carteCarburant = CarteCarburant::create($validated);
        return response()->json($carteCarburant, 201);
    }

    public function show(CarteCarburant $carteCarburant)
    {
        return response()->json($carteCarburant);
    }

    public function update(Request $request, CarteCarburant $carteCarburant)
    {
        $validated = $request->validate([
            'n_carte'          => 'required|string|max:255|unique:carte_carburants,n_carte,' . $carteCarburant->id,
            'plafond_caburant' => 'required|numeric',
            'unite'            => 'required|string|max:50',
            'plafond_service' => 'required|numeric',
        ]);

        $carteCarburant->update($validated);
        return response()->json($carteCarburant);
    }

    public function destroy(CarteCarburant $carteCarburant)
    {
        $carteCarburant->delete();
        return response()->json(null, 204);
    }
}
