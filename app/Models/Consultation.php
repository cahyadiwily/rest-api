<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'status',
        'desease_history',
        'current_symptoms',
        'doctor_notes',
        'doctor_id'
    ];
}
