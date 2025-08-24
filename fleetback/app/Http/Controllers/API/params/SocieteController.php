<?php

namespace App\Http\Controllers\API\params;

use App\Http\Controllers\Controller;
use App\Http\Resources\NameResource;
use App\Models\Societe;
use App\Traits\HasQueryFilters;
use Illuminate\Http\Request;

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
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:societes,nom',
        ]);

        $societe = Societe::create($validated);
        return response()->json($societe, 201);
    }

    public function show(Societe $societe)
    {
        return response()->json($societe);
    }

    public function update(Request $request, Societe $societe)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:societes,nom,' . $societe->id,
        ]);

        $societe->update($validated);
        return response()->json($societe);
    }

    public function destroy(Societe $societe)
    {
        $societe->delete();
        return response()->json(null, 204);
    }
}
