<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_name', 
        'supplier', 
        'quantity', 
        'price', 
        'image',
        'gender',
        'type',
        'color_stock',
        'new_stock_added',
    ];

    protected $casts = [
        'images' => 'array', // Cast images as an array
    ];

    // This will store the original quantity before the update
    protected $originalQuantity = 0;

    public static function boot()
    {
        parent::boot();

        // Listen for the updating event to track the previous quantity
        static::updating(function ($product) {
            $product->originalQuantity = $product->getOriginal('quantity');
        });
    }

    // Calculate the total sold
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function totalSold()
    {
        // Sum the quantity of reservations with status 'picked_up'
        $reservationsTotal = $this->reservations()->where('status', 'picked_up')->sum('quantity');
        
        // Sum the quantity of glasses sold for this product
        $glassesTotal = Glass::where('product_id', $this->id)->orWhere('lens_id', $this->id)->count();

        // Return the combined total
        return $reservationsTotal + $glassesTotal;
    }

    public function soldPerColor()
    {
        // Decode the color_stock field from JSON
        $colorStockArray = json_decode($this->color_stock, true); // true to get an associative array

        if (!$colorStockArray || !is_array($colorStockArray)) {
            return [];
        }

        // Initialize an array to hold the sold quantities per color
        $soldColors = [];

        // Get all reservations where the status is 'picked_up'
        $reservations = $this->reservations()->where('status', 'picked_up')->get();

        foreach ($reservations as $reservation) {
            $reservationColor = $reservation->color; // Adjust this to match the field name in Reservation

            // Find the color in the decoded color stock array
            foreach ($colorStockArray as $colorStock) {
                if ($colorStock['color'] == $reservationColor) {
                    // Initialize the sold color count if it doesn't exist
                    if (!isset($soldColors[$reservationColor])) {
                        $soldColors[$reservationColor] = 0;
                    }

                    // Add the quantity sold for this color
                    $soldColors[$reservationColor] += $reservation->quantity;
                }
            }
        }

        return $soldColors;
    }



    // Calculate the new stock added during update
    public function newStockAdded()
    {
        $newStock = $this->quantity - $this->originalQuantity;

        return $newStock > 0 ? $newStock : 0; // Only return positive stock additions
    }
}
