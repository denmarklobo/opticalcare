<?php

namespace App\Http\Controllers;

use App\Models\Glass;
use Illuminate\Http\Request;

class GlassController extends Controller
{
    public function index()
    {
        $glass = Glass::all();
        return response()->json($glass);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'frame' => 'nullable|string',
                'type_of_lens' => 'nullable|string',
                'remarks' => 'nullable|string',
            ]);

            $glass = Glass::create($request->all());

            return response()->json($glass, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show(Glass $glass)
    {
        return response()->json($glass);
    }

    public function update(Request $request, Glass $glass)
    {
        try {
            $request->validate([
                'frame' => 'nullable|string',
                'type_of_lens' => 'nullable|string',
                'remarks' => 'nullable|string',
            ]);

            $glass->update($request->all());

            return response()->json($glass, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Glass $glass)
    {
        try {
            $glass->delete();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
