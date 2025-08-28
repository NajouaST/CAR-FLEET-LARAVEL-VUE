<?php

namespace App\Http\Controllers\API\parc;

use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentVehiculeResource;
use App\Http\Resources\NameResource;
use App\Http\Resources\VehiculeResource;
use App\Models\DocumentVehicule;
use App\Models\Modele;
use App\Models\Vehicule;
use App\Traits\HasQueryFilters;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DocumentVehiculeController extends Controller
{
    use HasQueryFilters;

    public function index(Request $request)
    {
        $authUser = Auth::user();

        if ($authUser->can('vehicules view (all)')) {
            $query = DocumentVehicule::with('vehicule');
        } elseif ($authUser->can('vehicules view (own)')) {
            $query = DocumentVehicule::with('vehicule')
                ->whereHas('vehicule', function ($q) use ($authUser) {
                    $q->where('assigned_to', $authUser->id);
                });
        } else {
            return response()->json(['message' => 'No Access'], 403);
        }

        $result = $this->applyQueryParameters($query, $request);

        return [
            'code' => 200,
            'message' => 'Documents retrieved',
            'data' => DocumentVehiculeResource::collection($result['data']),
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

        DocumentVehicule::create($data);

        return [
            'code' => 201,
            'message' => 'Document created successfully',
        ];
    }

    public function show(DocumentVehicule $documentVehicule)
    {
        $authUser = Auth::user();

        if ($authUser->can('vehicules view (all)') ||
            ($authUser->can('vehicules view (own)') &&
                $documentVehicule->vehicule->assigned_to == $authUser->id)) {
            return $documentVehicule->load('vehicule');
        }

        return response()->json(['message' => 'No Access'], 403);
    }

    public function update(Request $request, DocumentVehicule $documentVehicule)
    {
        $authUser = Auth::user();

        if ($authUser->can('vehicules edit (all)') ||
            ($authUser->can('vehicules edit (own)') &&
                $documentVehicule->vehicule->assigned_to == $authUser->id)) {

            $data = $this->validateRequest($request, $documentVehicule->id);
            $data = $this->handleFiles($request, $data, $documentVehicule);

            $documentVehicule->update($data);

            return [
                'code' => 200,
                'message' => 'Document updated successfully',
            ];
        }

        return response()->json(['message' => 'No Access'], 403);
    }

    public function destroy(DocumentVehicule $documentVehicule)
    {
        $authUser = Auth::user();

        if ($authUser->can('vehicules delete (all)') ||
            ($authUser->can('vehicules delete (own)') &&
                $documentVehicule->vehicule->assigned_to == $authUser->id)) {

            if ($documentVehicule->image) {
                Storage::disk('public')->delete($documentVehicule->image);
            }

            $documentVehicule->delete();

            return [
                'code' => 200,
                'message' => 'Document deleted successfully',
            ];
        }

        return response()->json(['message' => 'No Access'], 403);
    }

    // -----------------------
    // Helpers
    // -----------------------
    private function validateRequest(Request $request, $documentId = null)
    {

        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('document_vehicules')
                    ->where('vehicule_id', $request->vehicule_id)
                    ->ignore($documentId),
            ],
            'note'        => 'nullable|string|max:1000',
            'image'       => 'nullable|image|max:4096',
            'vehicule_id' => 'required|exists:vehicules,id',
        ]);
    }

    private function handleFiles(Request $request, array $data, DocumentVehicule $document = null)
    {
        if ($request->hasFile('image')) {
            if ($document && $document->image) {
                Storage::disk('public')->delete($document->image);
            }
            $data['image'] = $request->file('image')->store('vehicules/documents', 'public');
        }

        return $data;
    }
}
