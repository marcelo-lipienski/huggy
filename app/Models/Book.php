<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Book extends Model
{
    use HasFactory;

    /** @property array<int, string> $fillable */
    protected $fillable = [
        'publisher_id',
        'title',
        'genre',
        'author',
        'year',
        'pages',
        'language',
        'edition',
        'isbn',
    ];

    /**
     * @return BelongsTo<Publisher, Book>
     */
    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class, 'publisher_id');
    }
}
