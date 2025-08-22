<?php

namespace App\Http\Controllers\API\parc;

use App\Http\Controllers\Controller;
use App\Http\Resources\NameResource;
use App\Http\Resources\VehiculeResource;
use App\Models\Modele;
use App\Models\Vehicule;
use App\Traits\HasQueryFilters;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class VehiculeController extends Controller
{
    use HasQueryFilters;

    public function listImmatriculations()
    {
        $vehicules = Vehicule::select('id', 'immatriculation')->get();
        return (new VehiculeResource($vehicules))->additional(['mode' => 'minimal']);
    }

    public function index(Request $request)
    {
        $authUser = Auth::user();

        if ($authUser->can('vehicules view (all)')) {
            $query = Vehicule::with(['modele.marque', 'familleVehicule', 'fournisseur']);
        } elseif ($authUser->can('vehicules view (own)')) {
            $query = Vehicule::with(['modele.marque', 'familleVehicule', 'fournisseur'])
                ->where('assigned_to', $authUser->id);
        } else {
            return response()->json(['message' => 'No Access'], 403);
        }

        $result = $this->applyQueryParameters($query, $request);

        return [
            'code' => 200,
            'message' => 'Vehicules retrieved',
            'data' => VehiculeResource::collection($result['data'])->additional(['mode' => 'grid']),
            'totalRecords' => $result['totalRecords'],
        ];
    }

    public function store(Request $request)
    {
        $authUser = Auth::user();
        if (!$authUser->can('vehicules create')) {
            return response()->json(['message' => 'No Access'], 403);
        }

        $data = $this->validateRequest($request);
        $data = $this->handleFiles($request, $data);

        Vehicule::create($data);

        return [
            'code' => 201,
            'message' => 'Vehicule created successfully',
        ];
    }

    public function show(Vehicule $vehicule)
    {
        $authUser = Auth::user();

        if ($authUser->can('vehicules view (all)') ||
            ($authUser->can('vehicules view (own)') && $vehicule->assigned_to == $authUser->id)) {
            $vehicule->load(['modele.marque', 'familleVehicule', 'fournisseur']);
            return new VehiculeResource($vehicule);
        }

        return response()->json(['message' => 'No Access'], 403);
    }

    public function update(Request $request, Vehicule $vehicule)
    {
        $authUser = Auth::user();

        if ($authUser->can('vehicules edit (all)') ||
            ($authUser->can('vehicules edit (own)') && $vehicule->assigned_to == $authUser->id)) {

            $data = $this->validateRequest($request, $vehicule->id);
            $data = $this->handleFiles($request, $data, $vehicule);

            $vehicule->update($data);

            return [
                'code' => 200,
                'message' => 'Vehicule updated successfully',
            ];
        }

        return response()->json(['message' => 'No Access'], 403);
    }

    public function destroy(Vehicule $vehicule)
    {
        $authUser = Auth::user();

        if ($authUser->can('vehicules delete (all)') ||
            ($authUser->can('vehicules delete (own)') && $vehicule->assigned_to == $authUser->id)) {

            if ($vehicule->carte_grise_front) Storage::disk('public')->delete($vehicule->carte_grise_front);
            if ($vehicule->carte_grise_back)  Storage::disk('public')->delete($vehicule->carte_grise_back);

            $vehicule->delete();

            return [
                'code' => 200,
                'message' => 'Vehicule deleted successfully',
            ];
        }

        return response()->json(['message' => 'No Access'], 403);
    }

    // -----------------------
    // Helpers
    // -----------------------
    private function validateRequest(Request $request, $vehiculeId = null)
    {
        $uniqueImmat = 'unique:vehicules,immatriculation' . ($vehiculeId ? ',' . $vehiculeId : '');
        $uniqueChassis = 'unique:vehicules,chassis' . ($vehiculeId ? ',' . $vehiculeId : '');

        return $request->validate([
            'immatriculation'     => 'required|string|max:50|' . $uniqueImmat,
            'chassis'             => 'required|string|max:100|' . $uniqueChassis,
            'carte_grise'         => 'nullable|string|max:255',
            'carte_grise_front'   => 'nullable|image|max:2048',
            'carte_grise_back'    => 'nullable|image|max:2048',
            'dmc'                 => 'nullable|date',
            'couleur'             => 'nullable|string|max:50',
            'categorie'           => 'nullable|string|max:50',
            'situation'           => 'required|in:en_exploitation,en_reparation,accidentee,arretee,litige,epave,vondue',
            'statut'              => 'required|in:libre,affectee,hors_service,vondue',
            'formule_achat'       => 'required|in:fond propre,leasing,LLD',
            'date'                => 'nullable|date',
            'valeur'              => 'nullable|numeric',
            'loyer'               => 'nullable|numeric',
            'date_garantie'       => 'nullable|date',
            'km_garantie'         => 'nullable|numeric',
            'date_cession'        => 'nullable|date',
            'valeur_cession'      => 'nullable|numeric',
            'modele_id'           => 'required|exists:modeles,id',
            'famille_vehicule_id' => 'required|exists:famille_vehicules,id',
            'fournisseur_id'      => 'required|exists:fournisseurs,id',
            'assigned_to'      => 'exists:users,id',
        ]);
    }

    private function handleFiles(Request $request, array $data, Vehicule $vehicule = null)
    {
        if ($request->hasFile('carte_grise_front')) {
            if ($vehicule && $vehicule->carte_grise_front) {
                Storage::disk('public')->delete($vehicule->carte_grise_front);
            }
            $data['carte_grise_front'] = $request->file('carte_grise_front')->store('vehicules/carte_grise', 'public');
        }

        if ($request->hasFile('carte_grise_back')) {
            if ($vehicule && $vehicule->carte_grise_back) {
                Storage::disk('public')->delete($vehicule->carte_grise_back);
            }
            $data['carte_grise_back'] = $request->file('carte_grise_back')->store('vehicules/carte_grise', 'public');
        }

        return $data;
    }
}
