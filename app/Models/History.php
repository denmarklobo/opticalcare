<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;

    protected $fillable = [
        'history_updated',
        'medical_history',
        'ocular_history',
    ];

    protected $dates = [
        'history_updated',
    ];
}
