<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return response()->json($products);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'nullable|string',
                'product_name' => 'nullable|string',
                'supplier' => 'nullable|string',
                'quantity' => 'nullable|integer',
                'price' => 'nullable|numeric',
                'image' => 'nullable|string',
            ]);

            $product = Product::create($request->all());

            return response()->json($product, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show(Product $product)
    {
        return response()->json($product);
    }

    public function update(Request $request, Product $product)
    {
        try {
            $request->validate([
                'product_id' => 'nullable|string',
                'product_name' => 'nullable|string',
                'supplier' => 'nullable|string',
                'quantity' => 'nullable|integer',
                'price' => 'nullable|numeric',
                'image' => 'nullable|string',
            ]);

            $product->update($request->all());

            return response()->json($product, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Product $product)
    {
        try {
            $product->delete();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
