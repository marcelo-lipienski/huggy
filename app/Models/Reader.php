<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    /**
     * @return BelongsToMany<Book>
     */
    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'readers_books');
    }
}
