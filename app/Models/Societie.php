<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Societie extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_card_number',
        'password',
        'login_tokens',
    ];
}
