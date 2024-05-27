<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'fname',
        'lname',
        'mname',
        'extension',
        'email',
        'address',
        'contact',
        'birthdate',
        'password',
    ];
}
