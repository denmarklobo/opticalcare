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
        'user_id',
    ];

     public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function prescription()
    {
        return $this->hasMany(Prescription::class);
    }

    public function glasses()
    {
        return $this->hasMany(Glass::class);
    }
    
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
    
}
