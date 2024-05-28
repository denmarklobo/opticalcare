<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    public function index($patient_id)
    {
        // Fetch prescriptions for the specified patient ID
        $prescriptions = Prescription::where('patient_id', $patient_id)->get();
        return response()->json($prescriptions);
    }

    public function store(Request $request, $patient_id)
    {
        try {
            $request->validate([
                // Validate your request fields here
            ]);

            // Create a new prescription for the specified patient ID
            $prescriptionData = $request->all();
            $prescriptionData['patient_id'] = $patient_id;
            $prescription = Prescription::create($prescriptionData);

            return response()->json($prescription, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($patient_id, Prescription $prescription)
    {
        // Make sure the requested prescription belongs to the specified patient
        if ($prescription->patient_id != $patient_id) {
            return response()->json(['error' => 'Prescription not found for the specified patient'], 404);
        }

        return response()->json($prescription);
    }

    public function update(Request $request, $patient_id, Prescription $prescription)
    {
        try {
            $request->validate([
                // Validate your request fields here
            ]);

            // Update the prescription if it belongs to the specified patient
            if ($prescription->patient_id != $patient_id) {
                return response()->json(['error' => 'Prescription not found for the specified patient'], 404);
            }

            $prescription->update($request->all());

            return response()->json($prescription, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($patient_id, Prescription $prescription)
    {
        try {
            // Make sure the requested prescription belongs to the specified patient
            if ($prescription->patient_id != $patient_id) {
                return response()->json(['error' => 'Prescription not found for the specified patient'], 404);
            }

            $prescription->delete();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
