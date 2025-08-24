<?php

namespace App\Http\Controllers\API\params;

use App\Http\Controllers\Controller;
use App\Http\Resources\NameResource;
use App\Models\Site;
use App\Traits\HasQueryFilters;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    use HasQueryFilters;

    public function index(Request $request)
    {
        $query = Site::query();
        $result = $this->applyQueryParameters($query, $request);
        return response()->json($result);
    }

    public function listNames()
    {
        // $sites = Site::all();
        // return NameResource::collection($sites);
        return Site::select('id','nom')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:sites,nom',
        ]);

        $site = Site::create($validated);
        return response()->json($site, 201);
    }

    public function show(Site $site)
    {
        return response()->json($site);
    }

    public function update(Request $request, Site $site)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:sites,nom,' . $site->id,
        ]);

        $site->update($validated);
        return response()->json($site);
    }

    public function destroy(Site $site)
    {
        $site->delete();
        return response()->json(null, 204);
    }
}