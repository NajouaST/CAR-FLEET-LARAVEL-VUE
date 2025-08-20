<?php

namespace App\Http\Controllers\API\parc;

use App\Http\Controllers\Controller;
use App\Http\Resources\NameResource;
use App\Models\Marque;
use App\Traits\HasQueryFilters;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MarqueController extends Controller
{
    use HasQueryFilters;
    public function listNames()
    {
        $marques = Marque::all();

        return NameResource::collection($marques);
    }

    public function index(Request $request)
    {
        $query = Marque::query();

        $result = $this->applyQueryParameters($query, $request);

        return response()->json($result);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('marques', 'public');
        }

        $marque = Marque::create($data);

        return response()->json($marque, 201);
    }

    public function show(Marque $marque)
    {
        return $marque;
    }

    public function update(Request $request, Marque $marque)
    {
        $data = $request->validate([
            'name'  => 'sometimes|required|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($marque->image) {
                Storage::disk('public')->delete($marque->image);
            }
            $data['image'] = $request->file('image')->store('marques', 'public');
        }

        $marque->update($data);

        return response()->json($marque);
    }

    public function destroy(Marque $marque)
    {
        if ($marque->image) {
            Storage::disk('public')->delete($marque->image);
        }
        $marque->delete();
        return response()->noContent();
    }
}
