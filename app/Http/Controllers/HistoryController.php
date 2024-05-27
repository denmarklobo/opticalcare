<?php

namespace App\Http\Controllers;

use App\Models\History;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index()
    {
        $history = History::all();
        return response()->json($history);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'history_updated' => 'nullable|date',
                'medical_history' => 'nullable|string',
                'ocular_history' => 'nullable|string',
            ]);

            $history = History::create($request->all());

            return response()->json($history, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show(History $history)
    {
        return response()->json($history);
    }

    public function update(Request $request, History $history)
    {
        try {
            $request->validate([
                'history_updated' => 'nullable|date',
                'medical_history' => 'nullable|string',
                'ocular_history' => 'nullable|string',
            ]);

            $history->update($request->all());

            return response()->json($history, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(History $history)
    {
        try {
            $history->delete();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
