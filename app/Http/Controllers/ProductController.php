<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{ 
    public function index()
    {
        $products = Product::all()->map(function ($product) {
            $product->image = asset('http://127.0.0.1:8000/' . $product->image);
            return $product;
        });
        return response()->json($products);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_name' => 'required|string',
            'supplier' => 'nullable|string',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
            'product_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $productImage = null;
        if ($request->hasFile('product_image')) {
            $productImage = $request->file('product_image')->store('product_images', 'public');
        }

        $product = new Product([
            'product_name' => $request->product_name,
            'supplier' => $request->supplier,
            'quantity' => $request->quantity,
            'price' => $request->price,
            'image' => $productImage,
        ]);

        $product->save();

        return response()->json($product, 201);
    }

    public function show(Product $product)
    {
        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);

            // Validate incoming request data
            $request->validate([
                'product_name' => 'required|string',
                'supplier' => 'required|string',
                'quantity' => 'required|integer',
                'price' => 'required|numeric',
            ]);

            // Update product attributes
            $product->product_name = $request->input('product_name');
            $product->supplier = $request->input('supplier');
            $product->quantity = $request->input('quantity');
            $product->price = $request->input('price');

            // Save the updated product
            $product->save();

            return response()->json($product);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Product not found or could not be updated'], 404);
        }
    }
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['message' => 'Product deleted successfully']);
    }

    public function getLatestProducts()
    {
        // Fetch the latest 4 products
        $products = Product::orderBy('created_at', 'desc')->take(4)->get();

        return response()->json($products);
    }

}
