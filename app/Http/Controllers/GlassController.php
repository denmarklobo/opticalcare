<?php

namespace App\Http\Controllers;

use App\Models\Glass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;

class GlassController extends Controller
{
    public function index($patient_id)
    {
        $glasses = Glass::where('patient_id', $patient_id)
            ->with(['product', 'lens']) 
            ->get();

        return response()->json($glasses);
    }


   public function store(Request $request)
    {
        try {
            $request->validate([
                'patient_id' => 'required',
                'product_id' => 'required', // This can be either an existing product or "other"
                'lens_id' => 'required',    // This can be either an existing lens or "other"
            ]);

            $glassData = $request->all();

            // If 'product_id' is 'other', store the custom frame directly in the 'Glass' model
            if ($request->product_id === 'other') {
                $glassData['custom_frame'] = $request->input('customFrame'); // Assuming you have a 'custom_frame' column in the 'Glass' model
                $glassData['product_id'] = null; // No product ID for custom frames
            }

            // If 'lens_id' is 'other', store the custom lens directly in the 'Glass' model
            if ($request->lens_id === 'other') {
                $glassData['custom_lens'] = $request->input('customLens'); // Assuming you have a 'custom_lens' column in the 'Glass' model
                $glassData['lens_id'] = null; // No lens ID for custom lenses
            }

            $glass = Glass::create($glassData);

            if ($glass->product_id) {
                $product = Product::findOrFail($glass->product_id);
                $product->decrement('quantity', 1);
            }

            if ($glass->lens_id) {
                $lens = Product::findOrFail($glass->lens_id);
                $lens->decrement('quantity', 1);
            }

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
    public function destroy(Request $request, $patient_id, $glasses_id)
        {
            // Validate the request parameters
            $validator = Validator::make([
                'patient_id' => $patient_id,
                'id' => $glasses_id,
            ], [
                'patient_id' => 'required|integer|exists:patients,id',
                'id' => 'required|integer|exists:glasses,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            try {
                // Find the glasses information
                $glasses = Glass::findOrFail($glasses_id);

                // Check if the glasses information belongs to the specified patient
                if ($glasses->patient_id == $patient_id) {
                    // Delete the glasses information
                    $glasses->delete();

                    return response()->json(['message' => 'Glasses information deleted successfully'], 200);
                } else {
                    return response()->json(['error' => 'Glasses information not found for the specified patient'], 404);
                }
            } catch (\Exception $e) {
                return response()->json(['error' => 'Failed to delete glasses information', 'message' => $e->getMessage()], 500);
            }
        }
}