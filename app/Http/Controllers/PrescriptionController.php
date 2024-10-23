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

    public function show($patient_id, $prescriptionId)
    {
        $prescription = Prescription::with('patient') 
                                    ->where('patient_id', $patient_id)
                                    ->where('id', $prescriptionId)
                                    ->first();

        if (!$prescription) {
            return response()->json(['error' => 'Prescription not found for the specified patient'], 404);
        }

        return response()->json($prescription);
    }


    public function update(Request $request, $patient_id, $prescriptionId)
    {
        try {
            // Validate your request fields here
            $request->validate([
                'right_eye_sphere' => 'required|string',
                'left_eye_sphere' => 'required|string',
                'right_eye_cylinder' => 'required|string',
                'left_eye_cylinder' => 'required|string',
                'right_eye_axis' => 'required|string',
                'left_eye_axis' => 'required|string',
                'reading_add' => 'required|string',
                'PD' => 'required|string',
                'right_eye_best_visual_acuity' => 'required|string',
                'left_eye_best_visual_acuity' => 'required|string',
            ]);

            // Update the prescription if it belongs to the specified patient
            $prescription = Prescription::where('patient_id', $patient_id)
                ->where('id', $prescriptionId)
                ->first();

            if (!$prescription) {
                return response()->json(['error' => 'Prescription not found for the specified patient'], 404);
            }

            // Update the prescription with validated data
            $prescription->fill($request->all());
            $prescription->save();

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