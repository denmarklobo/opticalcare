<?php

namespace App\Http\Controllers;

use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HistoryController extends Controller
{
    public function index($patient_id)
    {
        $history = History::where('patient_id', $patient_id)->get();
        return response()->json($history);
    }

   public function store(Request $request)
    {
        try {
            // Validate the request fields
            $validator = Validator::make($request->all(), [
                'patient_id' => 'required|exists:patients,id',
                'medical_history' => 'required|string',
                'ocular_history' => 'required|string'
                // Add more validation rules as needed
            ]);

            // Check if validation fails
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            // Create a new medical history for the specified patient ID
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

    public function destroy($patient_id, $history_id)
    {
        // Validate the request parameters
        $validator = Validator::make([
            'patient_id' => $patient_id,
            'id' => $history_id,
        ], [
            'patient_id' => 'required|integer|exists:patients,id',
            'id' => 'required|integer|exists:histories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        try {
            // Find the history
            $history = History::findOrFail($history_id);

            // Check if the history belongs to the specified patient
            if ($history->patient_id == $patient_id) {
                // Delete the history
                $history->delete();

                return response()->json(['message' => 'Medical history deleted successfully'], 200);
            } else {
                return response()->json(['error' => 'Medical history not found for the specified patient'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete medical history', 'message' => $e->getMessage()], 500);
        }
    }
}
