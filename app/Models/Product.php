<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage; // Corrected namespace for Storage facade

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
        'type'
    ];

    protected $casts = [
        'images' => 'array', // Cast images as an array
    ];

      public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
