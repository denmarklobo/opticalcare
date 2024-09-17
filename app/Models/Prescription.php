<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;


    protected $fillable = [
        'patient_id',
        'left_eye_sphere',
        'right_eye_sphere',
        'left_eye_cylinder',
        'right_eye_cylinder',
        'left_eye_axis',
        'right_eye_axis',
        'reading_add',
        'left_eye_best_visual_acuity',
        'right_eye_best_visual_acuity',
        'PD',
        'date',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
}
