<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Reader\Services;

use App\Domain\Reader\Services\MarkBookAsRead;
use App\Models\Book;
use App\Models\Reader;
use App\Models\ReaderBook;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class MarkBookAsReadTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_mark_book_as_read(): void
    {
        $givenReader = Reader::factory()->create();
        $givenBook = Book::factory()->create();

        Cache::shouldReceive('put')
            ->once()
            ->with(
                "reader:{$givenReader->id}",
                [
                    'books' => 1,
                    'pages' => $givenBook->pages,
                ]
            );

        $markBookAsRead = new MarkBookAsRead($givenReader, $givenBook);
        $markBookAsRead->execute();

        $this->assertDatabaseHas(ReaderBook::class, [
            'reader_id' => $givenReader->id,
            'book_id' => $givenBook->id,
        ]);
    }
}
