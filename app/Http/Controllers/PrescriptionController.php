<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PrescriptionController extends Controller
{
    public function index($patient_id)
    {
        $prescriptions = Prescription::where('patient_id', $patient_id)->get();
        return response()->json($prescriptions);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                // Validate your request fields here
            ]);

            // Create a new prescription for the specified patient ID
            $prescriptionData = $request->all();
            $prescription = Prescription::create($prescriptionData);

            return response()->json($prescription, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($patient_id, $prescription_id)
    {
        // Fetch the prescription for the specified patient ID and prescription ID
        $prescription = Prescription::where('patient_id', $patient_id)
                                     ->where('prescription_id', $prescription_id)
                                     ->first();

        if (!$prescription) {
            return response()->json(['error' => 'Prescription not found for the specified patient'], 404);
        }

        return response()->json($prescription);
    }

    public function update(Request $request, $patient_id, $prescription_id)
    {
        try {
            $request->validate([
                // Validate your request fields here
            ]);

            // Update the prescription if it belongs to the specified patient
            $prescription = Prescription::where('patient_id', $patient_id)
                                         ->where('id', $prescription_id)
                                         ->first();

            if (!$prescription) {
                return response()->json(['error' => 'Prescription not found for the specified patient'], 404);
            }

            $prescription->update($request->all());

            return response()->json($prescription, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

        public function destroy(Request $request, $patient_id, $prescription_id)
        {
            // Validate the request parameters
            $validator = Validator::make([
                'patient_id' => $patient_id,
                'id' => $prescription_id,
            ], [
                'patient_id' => 'required|integer|exists:patients,id',
                'id' => 'required|integer|exists:prescriptions,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            try {
                // Find the prescription
                $prescription = Prescription::findOrFail($prescription_id);

                // Check if the prescription belongs to the specified patient
                if ($prescription->patient_id == $patient_id) {
                    // Delete the prescription
                    $prescription->delete();

                    return response()->json(['message' => 'Prescription deleted successfully'], 200);
                } else {
                    return response()->json(['error' => 'Prescription not found for the specified patient'], 404);
                }
            } catch (\Exception $e) {
                return response()->json(['error' => 'Failed to delete prescription', 'message' => $e->getMessage()], 500);
            }
        }
    }