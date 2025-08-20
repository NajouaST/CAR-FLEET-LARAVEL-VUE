<?php

namespace App\Http\Controllers\API\parc;

use App\Http\Controllers\Controller;
use App\Http\Resources\NameResource;
use App\Models\Gamme;
use App\Traits\HasQueryFilters;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GammeController extends Controller
{
    use HasQueryFilters;
    public function index(Request $request)
    {
        $query = Gamme::query();

        $result = $this->applyQueryParameters($query, $request);

        return response()->json($result);
    }

    public function listNames()
    {
        $gamme = Gamme::all();

        return NameResource::collection($gamme);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:type_compteurs,name',
        ]);

        $gamme = Gamme::create($validated);

        return response()->json($gamme, 201);
    }

    public function show(Gamme $gamme)
    {
        return response()->json($gamme);
    }

    public function update(Request $request, Gamme $gamme)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:type_compteurs,name,' . $gamme->id,
        ]);

        $gamme->update($validated);

        return response()->json($gamme);
    }

    public function destroy(Gamme $gamme)
    {
        $gamme->delete();

        return response()->json(null, 204);
    }
}
