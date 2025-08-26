<?php

namespace App\Http\Controllers\API\RH;

use App\Http\Controllers\Controller;
use App\Http\Resources\NameResource;
use App\Models\Personnel;
use App\Traits\HasQueryFilters;
use Illuminate\Http\Request;


class PersonnelController extends Controller
{
    use HasQueryFilters;

    public function index(Request $request)
    {
        $query = Personnel::with([
			'user',
            'societe',
            'direction',
            'fonction',
            'region',
            'zone',
            'site',
            'departement',
            'grade',
            'division',
            'carteCarburant',
            'centreCout'
        ]);

        $result = $this->applyQueryParameters($query, $request);

        return response()->json($result);
    }

    public function listNames(Request $request)
    {
        $items = Personnel::select('matriculation', 'nom', 'cin')->get();
        return NameResource::collection($items);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
	{
		$validated = $request->validate([
			'matriculation'   => 'nullable|string|max:255',
			'cin'             => 'nullable|string|max:255',
			'societe_id'      => 'nullable|integer',
			'direction_id'    => 'nullable|integer',
			'fonction_id'     => 'nullable|integer',
			'region_id'       => 'nullable|integer',
			'zone_id'         => 'nullable|integer',
			'site_id'         => 'nullable|integer',
			'departement_id'  => 'nullable|integer',
			'grade_id'        => 'nullable|integer',
			'division_id'     => 'nullable|integer',
			'centre_cout_id'  => 'nullable|integer',
			'carte_carburant_id'  => 'nullable|integer',
			'user_id'  => 'nullable|integer',
			'tel'             => 'nullable|string|max:50',
			'superviseur'     => 'nullable|string|max:255',
			'titre'           => 'nullable|string|max:255',
			'adresse'         => 'nullable|string|max:1000',
			'type'            => 'nullable|string|max:100',
			'sexe'            => 'nullable|string|max:20',
			'num_carte_carb'  => 'nullable|integer',
			'num_permis'      => 'nullable|string|max:255',
			'delivre_le'      => 'nullable|date',
			'fin_validite'    => 'nullable|date',
		]);

		$personnel = Personnel::create($validated);

		return response()->json($personnel, 201);
	}

    /**
     * Display the specified resource.
     */
    public function show(Personnel $personnel)
	{
		return response()->json($personnel->load([
            'societe', 'direction', 'fonction', 'region', 'zone', 'site',
            'departement', 'grade', 'division', 'centreCout'
        ]));
	}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Personnel $personnel)
	{
		$validated = $request->validate([
			'matriculation'   => 'sometimes|nullable|string|max:255',
			'cin'             => 'sometimes|nullable|string|max:255',
			'societe_id'      => 'sometimes|nullable|integer',
			'direction_id'    => 'sometimes|nullable|integer',
			'fonction_id'     => 'sometimes|nullable|integer',
			'region_id'       => 'sometimes|nullable|integer',
			'zone_id'         => 'sometimes|nullable|integer',
			'site_id'         => 'sometimes|nullable|integer',
			'departement_id'  => 'sometimes|nullable|integer',
			'grade_id'        => 'sometimes|nullable|integer',
			'division_id'     => 'sometimes|nullable|integer',
			'centre_cout_id'  => 'sometimes|nullable|integer',
            'carte_carburant_id'  => 'nullable|integer',
            'user_id'  => 'nullable|integer',
            'tel'             => 'sometimes|nullable|string|max:50',
			'superviseur'     => 'sometimes|nullable|string|max:255',
			'titre'           => 'sometimes|nullable|string|max:255',
			'adresse'         => 'sometimes|nullable|string|max:1000',
			'type'            => 'sometimes|nullable|string|max:100',
			'sexe'            => 'sometimes|nullable|string|max:20',
			'num_carte_carb'  => 'sometimes|nullable|integer',
			'num_permis'      => 'sometimes|nullable|string|max:255',
			'delivre_le'      => 'sometimes|nullable|date',
			'fin_validite'    => 'sometimes|nullable|date',
		]);

		$personnel->update($validated);

		return response()->json($personnel->load([
            'societe', 'direction', 'fonction', 'region', 'zone', 'site',
            'departement', 'grade', 'division', 'centreCout'
        ]));
	}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Personnel $personnel)
	{
		$personnel->delete();

		return response()->json(null, 204);
	}
}
