<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'email',
        'address',
        'contact',
        'birthdate',
        'password',
    ];
    public function prescription()
    {
        return $this->hasMany(Patient::class, 'prescription_id');
    }
}
