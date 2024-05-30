<?php

namespace App\Http\Controllers;

use App\Models\Glass;
use Illuminate\Http\Request;

class GlassController extends Controller
{
    public function index($patient_id)
    {
        // Fetch prescriptions for the specified patient ID (assuming Glass model)
        $glasses = Glass::where('patient_id', $patient_id)->get();
        return response()->json($glasses);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                // Validate your request fields here
                'patient_id' => 'required',
                // Add other validation rules as needed
            ]);

            // Create a new prescription for the specified patient ID (assuming Glass model)
            $glassData = $request->all();
            $glass = Glass::create($glassData);

            return response()->json($glass, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($patient_id, $glass_id)
    {
        // Fetch the prescription for the specified patient ID and glass ID (assuming Glass model)
        $glass = Glass::where('patient_id', $patient_id)
                      ->where('id', $glass_id)
                      ->first();

        if (!$glass) {
            return response()->json(['error' => 'Glass not found for the specified patient'], 404);
        }

        return response()->json($glass);
    }

    public function update(Request $request, $patient_id, $glass_id)
    {
        try {
            $request->validate([
                // Validate your request fields here
                'patient_id' => 'required',
                // Add other validation rules as needed
            ]);

            // Update the prescription if it belongs to the specified patient (assuming Glass model)
            $glass = Glass::where('patient_id', $patient_id)
                          ->where('id', $glass_id)
                          ->first();

            if (!$glass) {
                return response()->json(['error' => 'Glass not found for the specified patient'], 404);
            }

            $glass->update($request->all());

            return response()->json($glass, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($patient_id, $glass_id)
    {
        try {
            // Delete the prescription if it belongs to the specified patient (assuming Glass model)
            $glass = Glass::where('patient_id', $patient_id)
                          ->where('id', $glass_id)
                          ->first();

            if (!$glass) {
                return response()->json(['error' => 'Glass not found for the specified patient'], 404);
            }

            $glass->delete();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}