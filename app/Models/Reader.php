<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class Reader extends Model
{
    use HasFactory, Notifiable;

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

    protected static function booted(): void
    {
        static::creating(function (Reader $reader) {
            $reader->token = Str::random(30);
        });
    }

    /**
     * @return BelongsToMany<Book>
     */
    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'readers_books', 'reader_id', 'book_id');
    }
}
