<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    public function index()
    {
        $prescriptions = Prescription::all();
        return response()->json($prescriptions);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'left_eye_sphere' => 'required|string',
                'right_eye_sphere' => 'required|string',
                'left_eye_cylinder' => 'required|string',
                'right_eye_cylinder' => 'required|string',
                'left_eye_axis' => 'required|string',
                'right_eye_axis' => 'required|string',
                'reading_add' => 'required|string',
                'best_visual_acuity' => 'required|string',
                'PD' => 'required|string',
                'date' => 'required|string',
            ]);

            $prescription = Prescription::create($request->all());

            return response()->json($prescription, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show(Prescription $prescription)
    {
        return response()->json($prescription);
    }

    public function update(Request $request, Prescription $prescription)
    {
        try {
            $request->validate([
                'left_eye_sphere' => 'required|string',
                'right_eye_sphere' => 'required|string',
                'left_eye_cylinder' => 'required|string',
                'right_eye_cylinder' => 'required|string',
                'left_eye_axis' => 'required|string',
                'right_eye_axis' => 'required|string',
                'reading_add' => 'required|string',
                'best_visual_acuity' => 'required|string',
                'PD' => 'required|string',
                'date' => 'required|string',
            ]);

            $prescription->update($request->all());

            return response()->json($prescription, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Prescription $prescription)
    {
        try {
            $prescription->delete();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
