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
            // Decode the main product images
            $images = json_decode($product->image);

        if (!empty($images) && is_array($images)) {
            $product->images = array_map(function ($image) {
                return asset('http://127.0.0.1:8000/' . $image);
            }, $images);
        } else {
            $product->images = [];
        }

            // Process color_stock to update image paths
            $colorStock = json_decode($product->color_stock, true); // Decode the color_stock JSON
        if (!empty($colorStock) && is_array($colorStock)) {
            foreach ($colorStock as &$color) {
                if (!empty($color['image'])) {
                    // Update the image path for each color stock
                    $color['image'] = asset('http://127.0.0.1:8000/' . $color['image']);
                } else {
                    // Set to null or empty if no image
                    $color['image'] = null; // or [] if you prefer an empty array
                }
            }
        }

            // Assign the updated color_stock back to the product
            $product->color_stock = $product->color_stock; // This line is optional, just to clarify that you're setting it

            // Other properties
            $product->total_sold = $product->totalSold();
            $product->sold_per_color = $product->soldPerColor();
            $product->new_stock_added = $product->new_stock_added;

            // Remove the old image field
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
            'color_stock' => 'required|json',
            'color_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
         $productImages = [];

        // Check if there are images in the request
        if ($request->hasFile('product_images')) {
            foreach ($request->file('product_images') as $image) {
                // Store each image and add its path to the array
                $path = $image->store('product_images', 'public');
                $productImages[] = $path;
            }
        }

        $colorStock = json_decode($request->input('color_stock'), true);
        $colorImages = [];

        // Handle color images upload
        foreach ($colorStock as $index => $color) {
            if ($request->hasFile("color_images.$index")) {
                $imagePath = $request->file("color_images.$index")->store('color_images', 'public');
                $colorImages[$index] = $imagePath;
            }
        }

        // Merge color images with color stock
        foreach ($colorStock as $index => &$color) {
            if (isset($colorImages[$index])) {
                $color['image'] = $colorImages[$index];
            }
        }

        // Create a new product with the given details and save color stock as JSON
        $product = new Product([
            'product_name' => $request->product_name,
            'supplier' => $request->supplier,
            'quantity' => $request->quantity,
            'image' => json_encode($productImages),
            'price' => $request->price,
            'gender' => $request->input('gender'),
            'type' => $request->input('type'),
            'color_stock' => json_encode($colorStock), // Store color_stock JSON with image paths
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

        $colorStock = json_decode($product->color_stock, true); // Decode the color_stock JSON
        if (!empty($colorStock) && is_array($colorStock)) {
            foreach ($colorStock as &$color) {
                if (!empty($color['image'])) {
                    // Update the image path for each color stock
                    $color['image'] = asset('http://127.0.0.1:8000/' . $color['image']);
                } else {
                    // Set to null or empty if no image
                    $color['image'] = null; // or [] if you prefer an empty array
                }
            }
        }

        return response()->json($product);
    }

   public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'color_stock' => 'required|array',
            'color_stock.*.color' => 'required|string|max:50',
            'color_stock.*.stock' => 'required|integer|min:0',
            'color_stock.*.restockQuantity' => 'required|integer|min:0',
            'color_stock.*.image' => 'nullable|file|image|mimes:jpeg,png,jpg,gif|max:2048', // Image validation
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Fetch product
        $product = Product::findOrFail($id);

        // Update product attributes
        $product->quantity = $request->input('quantity');
        $product->price = $request->input('price');

        $colorStockData = [];
        foreach ($request->input('color_stock') as $index => $colorStock) {
            // Handle image upload
            if ($request->hasFile("color_stock.{$index}.image")) {
                $imagePath = $request->file("color_stock.{$index}.image")->store('public/color_stock');
                $colorStock['image'] = Storage::url($imagePath); // Save the image URL
            }

            $colorStockData[] = $colorStock;
        }

        // Save color stock data to the product
        $product->color_stock = json_encode($colorStockData);
        $product->save();

        return response()->json(['message' => 'Product updated successfully']);
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
