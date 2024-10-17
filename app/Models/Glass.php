<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Glass extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'frame',
        'type_of_lens',
        'remarks',
        'product_id',
        'lens_id',
        'price',
        'custom_frame',
        'custom_lens',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function lens()
    {
        return $this->belongsTo(Product::class, 'lens_id');
    }
}
