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

    public function uploadImage(Request $request)
    {
        // Validate the image
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Store the image and get the URL
        $imagePath = $request->file('image')->store('color_images', 'public');

        // Get the base URL, append 127.0.0.1, and return the full URL
        $baseUrl = 'http://127.0.0.1:8000'; // Add your desired base URL
        $imageUrl = asset('' . $imagePath);

        // Concatenate the base URL with the image path
        $fullImageUrl = $baseUrl . $imageUrl;

        // Return the full URL of the uploaded image
        return response()->json([
            'imageUrl' => $fullImageUrl,
        ]);
    }


    public function update(Request $request, $productId)
{
    // Validate the basic fields
    $request->validate([
        'product_name' => 'required|string|max:255',
        'price' => 'required|numeric',
        'quantity' => 'required|integer',
        'color_stock' => 'required|array',
        'color_stock.*.color' => 'required|string',
        'color_stock.*.stock' => 'required|integer',
    ]);

    // Custom validation for the image field in color_stock
    foreach ($request->input('color_stock') as $key => $color) {
        $image = $color['image'] ?? null;

        if ($image) {
            // Check if the image is a valid URL
            if (filter_var($image, FILTER_VALIDATE_URL)) {
                // Optionally strip out the domain part
                $color['image'] = preg_replace('/http:\/\/127\.0\.0\.1:8000\//', '', $image);
            } else {
                // Validate the image as a file
                $request->validate([
                    'color_stock.' . $key . '.image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Image validation
                ]);
            }
        }
    }

    // Find the product
    $product = Product::findOrFail($productId);

    // Update basic product information
    $product->product_name = $request->input('product_name');
    $product->price = $request->input('price');
    $product->quantity = $request->input('quantity');
    
    // Process the color_stock array
    $colorStock = [];

    foreach ($request->input('color_stock') as $color) {
        if (isset($color['image']) && filter_var($color['image'], FILTER_VALIDATE_URL)) {
            // Remove the domain part if it exists
            $color['image'] = preg_replace('/http:\/\/127\.0\.0\.1:8000\//', '', $color['image']);
        } elseif (isset($color['image']) && $color['image'] instanceof \Illuminate\Http\UploadedFile) {
            // If the image is an uploaded file, store it and get the relative path
            $imagePath = $color['image']->store('color_images', 'public');
            $color['image'] = 'storage/' . $imagePath;
        }

        // Add the processed color stock to the array
        $colorStock[] = $color;
    }

    // Update the color_stock field in the database
    $product->color_stock = json_encode($colorStock);

    // Save the updated product
    $product->save();

    return response()->json(['message' => 'Product updated successfully!']);
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
