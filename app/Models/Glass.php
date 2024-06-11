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
    ];
     public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
}
