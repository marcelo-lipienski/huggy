<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reader extends Model
{
    use HasFactory;

    /** @property array<int, string> $fillable */
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'address',
        'birthdate',
    ];

    protected $casts = [
        'birthdate' => 'datetime:Y-m-d',
    ];
}
