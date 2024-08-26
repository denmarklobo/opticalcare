<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'status',
        'picked_up_date',
    ];

    protected $casts = [
        'picked_up_date' => 'datetime', 
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'user_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
