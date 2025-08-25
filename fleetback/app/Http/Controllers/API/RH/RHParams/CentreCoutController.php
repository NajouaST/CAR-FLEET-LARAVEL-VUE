<?php

namespace App\Http\Controllers\API\RH\RHParams;

use App\Http\Controllers\Controller;
use App\Models\CentreCout;
use App\Traits\HasQueryFilters;
use Illuminate\Http\Request;

class CentreCoutController extends Controller
{
    use HasQueryFilters;

    public function index(Request $request)
    {
        $query = CentreCout::query();
        $result = $this->applyQueryParameters($query, $request);
        return response()->json($result);
    }

    public function listNames()
    {
        // $centreCouts = CentreCout::all();
        // return NameResource::collection($centreCouts);
        return CentreCout::select('id','nom')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:centre_couts,nom',
        ]);

        $centreCout = CentreCout::create($validated);
        return response()->json($centreCout, 201);
    }

    public function show(CentreCout $centreCout)
    {
        return response()->json($centreCout);
    }

    public function update(Request $request, CentreCout $centreCout)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:centre_couts,nom,' . $centreCout->id,
        ]);

        $centreCout->update($validated);
        return response()->json($centreCout);
    }

    public function destroy(CentreCout $centreCout)
    {
        $centreCout->delete();
        return response()->json(null, 204);
    }
}
