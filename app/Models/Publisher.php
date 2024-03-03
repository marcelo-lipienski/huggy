<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publisher extends Model
{
    use HasFactory;

    /** @property array<int, string> $fillable */
    public $fillable = [
        'name',
        'code',
        'phone_number',
    ];
}
