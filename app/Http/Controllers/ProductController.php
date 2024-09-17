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
            // Decode the JSON array of images
            $images = json_decode($product->image);

            // Check if there are any images and get the first one
            if (!empty($images) && is_array($images)) {
                $firstImage = $images[0]; // Get the first image path
                $product->image = asset('http://127.0.0.1:8000/' . $firstImage); // Use asset helper to generate URL
            } else {
                $product->image = null; // Set to null if no images found
            }

            return $product;
        });

        return response()->json($products);
    }

    public function newIndex()
    {
        $products = Product::all()->map(function ($product) {
            // Decode the JSON array of images
            $images = json_decode($product->image);

            // Check if there are any images
            if (!empty($images) && is_array($images)) {
                // Map each image path to its URL
                $product->images = array_map(function ($image) {
                    return asset('http://127.0.0.1:8000/' . $image);
                }, $images);
            } else {
                $product->images = []; // Set to an empty array if no images found
            }

            // Optionally, remove the original image attribute if you only want to return the new images attribute
            unset($product->image);

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
            'product_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gender' => 'required|in:Men,Women,Unisex',
            'type' => 'required|in:Frames,Lens,Contact Lenses,Accessories',
            'color_stock' => 'required|json', // Validate that color_stock is a valid JSON object
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Initialize an array to hold image paths
        $productImages = [];

        // Check if there are images in the request
        if ($request->hasFile('product_images')) {
            foreach ($request->file('product_images') as $image) {
                // Store each image and add its path to the array
                $path = $image->store('product_images', 'public');
                $productImages[] = $path;
            }
        }

        // Create a new product with the given details and save image paths and color_stock as JSON
        $product = new Product([
            'product_name' => $request->product_name,
            'supplier' => $request->supplier,
            'quantity' => $request->quantity,
            'price' => $request->price,
            'image' => json_encode($productImages), // Store multiple images as a JSON array
            'gender' => $request->input('gender'),
            'type' => $request->input('type'),
            'color_stock' => $request->input('color_stock'), // Store color_stock JSON as is
        ]);

        $product->save();

        return response()->json($product, 201);
    }


    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Decode the JSON array of images
        $images = json_decode($product->image);

        // Check if there are any images and process them
        if (!empty($images) && is_array($images)) {
            // Map each image path to its full URL
            $product->images = array_map(function ($image) {
                return asset('http://127.0.0.1:8000/' . $image);
            }, $images);
        } else {
            $product->images = []; // Return an empty array if no images found
        }

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
        $products = Product::orderBy('created_at', 'desc')->take(4)->get()->map(function ($product) {
            // Decode the JSON array of images
            $images = json_decode($product->image);

            // Check if there are any images and get the first one
            if (!empty($images) && is_array($images)) {
                // Get the first image path
                $firstImage = $images[0];
                // Generate URL for the first image
                $product->image = asset('http://127.0.0.1:8000/' . $firstImage); // Assuming images are stored in 'storage/app/public/product_images'
            } else {
                $product->image = null; // Set to null if no images found
            }

            return $product;
        });

        return response()->json($products);
    }


}
