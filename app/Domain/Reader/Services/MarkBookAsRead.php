<?php

declare(strict_types=1);

namespace App\Domain\Reader\Services;

use App\Models\Book;
use App\Models\Reader;
use Illuminate\Support\Facades\Cache;

final class MarkBookAsRead
{
    public function __construct(private Reader $reader, private Book $book)
    {
    }

    public function execute(): void
    {
        $this->reader->books()->syncWithoutDetaching($this->book);

        Cache::put("reader:{$this->reader->id}", [
            'books' => $this->reader->books->count(),
            'pages' => $this->reader->books->sum('pages'),
        ]);
    }
}
