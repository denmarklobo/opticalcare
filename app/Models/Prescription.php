<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'left_eye_sphere',
        'right_eye_sphere',
        'left_eye_cylinder',
        'right_eye_cylinder',
        'left_eye_axis',
        'right_eye_axis',
        'reading_add',
        'best_visual_acuity',
        'PD',
        'date',
    ];
}
