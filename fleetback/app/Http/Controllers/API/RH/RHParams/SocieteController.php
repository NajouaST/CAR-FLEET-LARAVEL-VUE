<?php

namespace App\Http\Controllers\API\RH\RHParams;

use App\Http\Controllers\Controller;
use App\Models\Societe;
use App\Traits\HasQueryFilters;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SocieteController extends Controller
{
    use HasQueryFilters;

    public function index(Request $request)
    {
        $query = Societe::query();
        $result = $this->applyQueryParameters($query, $request);
        return response()->json($result);
    }

    public function listNames()
    {
        // $societes = Societe::all();
        // return NameResource::collection($societes);

        return Societe::select('id','nom')->get();
    }

    public function store(Request $request)
    {
        \Log::info("incoming data (create):", $request->all());

        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:societes,nom',
            'description' => 'nullable|string|max:255',
            'logo_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            
        ]);

        if ($request->hasFile('logo_path')) {
            $validated['logo_path'] = $request->file('logo_path')->store('societes', 'public');
        }

        $societe = Societe::create($validated);
        return response()->json($societe, 201);
    }

    public function show(Societe $societe)
    {
        return response()->json($societe);
    }

    public function update(Request $request, Societe $societe)
    {
        \Log::info("Incoming request data (update):", $request->all());
        \Log::info("Incoming files (update):", $request->file());

        $validated = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:255',
            'logo_path' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'existing_logo_path' => 'sometimes|string',
        ]);

        \Log::info('Validated data:', $validated);

        // Handle NEW file upload
        if ($request->hasFile('logo_path')) {
            // Delete old logo if exists
            if ($societe->logo_path) {
                Storage::disk('public')->delete($societe->logo_path);
            }
            $validated['logo_path'] = $request->file('logo_path')->store('societes', 'public');
        } 
        // Handle existing logo - keep it
        else if ($request->has('existing_logo_path')) {
            $validated['logo_path'] = $request->existing_logo_path;
        }
        // If no logo data provided, don't change the logo field
        else {
            unset($validated['logo_path']);
        }

        $societe->update($validated);

        \Log::info('Updated societe:', $societe->fresh()->toArray());
        return response()->json($societe);
    }

    public function destroy(Societe $societe)
    {
        // Delete logo file if exists
        if ($societe->logo_path) {
            Storage::disk('public')->delete($societe->logo_path);
        }

        $societe->delete();
        return response()->json(null, 204);
    }
}
