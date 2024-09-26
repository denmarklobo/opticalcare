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
                'product_id' => 'required', 
                'lens_id' =>'required',
            ]);

            $glassData = $request->all();
            $glass = Glass::create($glassData);

            // Decrement the product quantity
            $product = Product::findOrFail($glass->product_id); 
            $product->decrement('quantity', 1);

            $product = Product::findOrFail($glass->lens_id); 
            $product->decrement('quantity', 1);

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