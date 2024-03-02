<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reader extends Model
{
    use HasFactory;

    /** @property array<int, string> $fillable */
    public $fillable = [
        'name',
        'email',
        'phone_number',
        'address',
        'birthdate',
    ];
}
