<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Book;
use App\Models\Publisher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class BookControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_an_empty_list_of_books(): void
    {
        $response = $this->getJson('/api/books');

        $response->assertStatus(200);
        $response->assertJson([]);
    }

    public function test_it_returns_a_list_of_books_ordered_by_most_recent(): void
    {
        [$givenFirstBook, $givenSecondBook, $givenThirdBook] = Book::factory()->count(3)->create();

        $response = $this->get('/api/books');

        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) => $json->has('data')
            ->has('data.0', fn (AssertableJson $json) => $json
                ->where('id', $givenThirdBook->id)
                ->where('title', $givenThirdBook->title)
                ->where('genre', $givenThirdBook->genre)
                ->where('author', $givenThirdBook->author)
                ->where('year', $givenThirdBook->year)
                ->where('pages', $givenThirdBook->pages)
                ->where('language', $givenThirdBook->language)
                ->where('edition', $givenThirdBook->edition)
                ->where('isbn', $givenThirdBook->isbn)
                ->etc()
            )
            ->has('data.1', fn (AssertableJson $json) => $json
                ->where('id', $givenSecondBook->id)
                ->where('title', $givenSecondBook->title)
                ->where('genre', $givenSecondBook->genre)
                ->where('author', $givenSecondBook->author)
                ->where('year', $givenSecondBook->year)
                ->where('pages', $givenSecondBook->pages)
                ->where('language', $givenSecondBook->language)
                ->where('edition', $givenSecondBook->edition)
                ->where('isbn', $givenSecondBook->isbn)
                ->etc()
            )
            ->has('data.2', fn (AssertableJson $json) => $json
                ->where('id', $givenFirstBook->id)
                ->where('title', $givenFirstBook->title)
                ->where('genre', $givenFirstBook->genre)
                ->where('author', $givenFirstBook->author)
                ->where('year', $givenFirstBook->year)
                ->where('pages', $givenFirstBook->pages)
                ->where('language', $givenFirstBook->language)
                ->where('edition', $givenFirstBook->edition)
                ->where('isbn', $givenFirstBook->isbn)
                ->etc()
            )
        );
    }

    public function test_it_returns_created_book(): void
    {
        $givenPublisher = Publisher::factory()->create();

        $givenAttributes = [
            'publisher_id' => $givenPublisher->id,
            'title' => fake()->text(),
            'genre' => fake()->word(),
            'author' => fake()->name(),
            'year' => (int) fake()->year(),
            'pages' => fake()->numberBetween(10, 999),
            'language' => fake()->word(),
            'edition' => fake()->numberBetween(1, 99),
            'isbn' => fake()->isbn13(),
        ];

        $response = $this->postJson('/api/books', $givenAttributes);
        $response->assertStatus(201);
        $response->assertJson(fn (AssertableJson $json) => $json->has('data', fn (AssertableJson $json) => $json
            ->where('publisher_id', $givenPublisher->id)
            ->where('title', $givenAttributes['title'])
            ->where('genre', $givenAttributes['genre'])
            ->where('author', $givenAttributes['author'])
            ->where('year', $givenAttributes['year'])
            ->where('pages', $givenAttributes['pages'])
            ->where('language', $givenAttributes['language'])
            ->where('edition', $givenAttributes['edition'])
            ->where('isbn', $givenAttributes['isbn'])
            ->etc()
        )
        );

        $this->assertDatabaseCount(Book::class, 1);
    }

    public function test_it_fails_to_create_book_with_an_already_used_isbn(): void
    {
        $givenPublisher = Publisher::factory()->create();

        $givenAttributes = [
            'publisher_id' => $givenPublisher->id,
            'title' => fake()->text(),
            'genre' => fake()->word(),
            'author' => fake()->name(),
            'year' => (int) fake()->year(),
            'pages' => fake()->numberBetween(10, 999),
            'language' => fake()->word(),
            'edition' => fake()->numberBetween(1, 99),
            'isbn' => fake()->isbn13(),
        ];

        $response = $this->postJson('/api/books', $givenAttributes);
        $response->assertStatus(201);
        $response->assertJson(fn (AssertableJson $json) => $json->has('data', fn (AssertableJson $json) => $json
            ->where('publisher_id', $givenPublisher->id)
            ->where('title', $givenAttributes['title'])
            ->where('genre', $givenAttributes['genre'])
            ->where('author', $givenAttributes['author'])
            ->where('year', $givenAttributes['year'])
            ->where('pages', $givenAttributes['pages'])
            ->where('language', $givenAttributes['language'])
            ->where('edition', $givenAttributes['edition'])
            ->where('isbn', $givenAttributes['isbn'])
            ->etc()
        )
        );

        $response = $this->postJson('/api/books', $givenAttributes);
        $response->assertStatus(422);

        $this->assertDatabaseCount(Book::class, 1);
    }

    /**
     * @param  array<string, array<string, string>>  $givenAttributes
     */
    #[DataProvider('invalidBookAttributesProvider')]
    public function test_it_returns_error_when_creating_book_with_invalid_attributes(array $givenAttributes): void
    {
        $givenPublisher = Publisher::factory()->create();

        $givenAttributes = array_merge($givenAttributes, ['publisher_id' => $givenPublisher->id]);

        $response = $this->postJson('/api/books', $givenAttributes);
        $response->assertStatus(422);
    }

    public static function invalidBookAttributesProvider(): Iterator
    {
        yield 'missing title' => [
            'givenAttributes' => [
                'genre' => fake()->word(),
                'author' => fake()->name(),
                'year' => (int) fake()->year(),
                'pages' => fake()->numberBetween(10, 999),
                'language' => fake()->word(),
                'edition' => fake()->numberBetween(1, 99),
                'isbn' => fake()->isbn13(),
            ],
        ];

        yield 'missing genre' => [
            'givenAttributes' => [
                'title' => fake()->text(),
                'author' => fake()->name(),
                'year' => (int) fake()->year(),
                'pages' => fake()->numberBetween(10, 999),
                'language' => fake()->word(),
                'edition' => fake()->numberBetween(1, 99),
                'isbn' => fake()->isbn13(),
            ],
        ];

        yield 'missing author' => [
            'givenAttributes' => [
                'title' => fake()->text(),
                'genre' => fake()->word(),
                'year' => (int) fake()->year(),
                'pages' => fake()->numberBetween(10, 999),
                'language' => fake()->word(),
                'edition' => fake()->numberBetween(1, 99),
                'isbn' => fake()->isbn13(),
            ],
        ];

        yield 'missing year' => [
            'givenAttributes' => [
                'title' => fake()->text(),
                'genre' => fake()->word(),
                'author' => fake()->name(),
                'pages' => fake()->numberBetween(10, 999),
                'language' => fake()->word(),
                'edition' => fake()->numberBetween(1, 99),
                'isbn' => fake()->isbn13(),
            ],
        ];

        yield 'missing pages' => [
            'givenAttributes' => [
                'title' => fake()->text(),
                'genre' => fake()->word(),
                'author' => fake()->name(),
                'year' => (int) fake()->year(),
                'language' => fake()->word(),
                'edition' => fake()->numberBetween(1, 99),
                'isbn' => fake()->isbn13(),
            ],
        ];

        yield 'missing language' => [
            'givenAttributes' => [
                'title' => fake()->text(),
                'genre' => fake()->word(),
                'author' => fake()->name(),
                'year' => (int) fake()->year(),
                'pages' => fake()->numberBetween(10, 999),
                'edition' => fake()->numberBetween(1, 99),
                'isbn' => fake()->isbn13(),
            ],
        ];

        yield 'missing edition' => [
            'givenAttributes' => [
                'title' => fake()->text(),
                'genre' => fake()->word(),
                'author' => fake()->name(),
                'year' => (int) fake()->year(),
                'pages' => fake()->numberBetween(10, 999),
                'language' => fake()->word(),
                'isbn' => fake()->isbn13(),
            ],
        ];

        yield 'missing isbn' => [
            'givenAttributes' => [
                'title' => fake()->text(),
                'genre' => fake()->word(),
                'author' => fake()->name(),
                'year' => (int) fake()->year(),
                'pages' => fake()->numberBetween(10, 999),
                'language' => fake()->word(),
                'edition' => fake()->numberBetween(1, 99),
            ],
        ];
    }

    public function test_it_returns_book_by_id(): void
    {
        $givenBook = Book::factory()->create();

        $response = $this->getJson("/api/books/{$givenBook->id}");
        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) => $json->has('data', fn (AssertableJson $json) => $json
            ->where('id', $givenBook->id)
            ->where('title', $givenBook->title)
            ->where('genre', $givenBook->genre)
            ->where('author', $givenBook->author)
            ->where('year', $givenBook->year)
            ->where('pages', $givenBook->pages)
            ->where('language', $givenBook->language)
            ->where('edition', $givenBook->edition)
            ->where('isbn', $givenBook->isbn)
            ->etc()
        )
        );
    }

    public function test_it_returns_error_when_book_does_not_exist(): void
    {
        $response = $this->getJson('/api/books/1');
        $response->assertStatus(404);
    }

    public function test_it_updates_book_by_id(): void
    {
        $givenBook = Book::factory()->create();
        $givenBookNewValues = [
            'title' => fake()->text(),
            'genre' => fake()->word(),
            'author' => fake()->name(),
            'year' => (int) fake()->year(),
            'pages' => fake()->numberBetween(10, 999),
            'language' => fake()->word(),
            'edition' => fake()->numberBetween(1, 99),
            'isbn' => fake()->isbn13(),
        ];

        $response = $this->putJson("/api/books/{$givenBook->id}", $givenBookNewValues);
        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) => $json->has('data', fn (AssertableJson $json) => $json
            ->where('id', $givenBook->id)
            ->where('publisher_id', $givenBook->publisher_id)
            ->where('title', $givenBookNewValues['title'])
            ->where('genre', $givenBookNewValues['genre'])
            ->where('author', $givenBookNewValues['author'])
            ->where('year', $givenBookNewValues['year'])
            ->where('pages', $givenBookNewValues['pages'])
            ->where('language', $givenBookNewValues['language'])
            ->where('edition', $givenBookNewValues['edition'])
            ->where('isbn', $givenBookNewValues['isbn'])
            ->etc()
        )
        );

        $this->assertDatabaseHas(Book::class, [
            'id' => $givenBook->id,
            'publisher_id' => $givenBook->publisher_id,
            'title' => $givenBookNewValues['title'],
            'genre' => $givenBookNewValues['genre'],
            'author' => $givenBookNewValues['author'],
            'year' => $givenBookNewValues['year'],
            'pages' => $givenBookNewValues['pages'],
            'language' => $givenBookNewValues['language'],
            'edition' => $givenBookNewValues['edition'],
            'isbn' => $givenBookNewValues['isbn'],
        ]);
    }

    public function test_it_returns_error_when_updating_a_non_existing_book(): void
    {
        $givenBookNewValues = [
            'title' => fake()->text(),
            'genre' => fake()->word(),
            'author' => fake()->name(),
            'year' => (int) fake()->year(),
            'pages' => fake()->numberBetween(10, 999),
            'language' => fake()->word(),
            'edition' => fake()->numberBetween(1, 99),
            'isbn' => fake()->isbn13(),
        ];

        $response = $this->putJson('/api/books/1', $givenBookNewValues);
        $response->assertStatus(404);
    }

    public function test_it_deletes_book_by_id(): void
    {
        $givenBook = Book::factory()->create();

        $response = $this->deleteJson("/api/books/{$givenBook->id}");
        $response->assertStatus(200);
        $this->assertDatabaseMissing(Book::class, [
            'id' => $givenBook->id,
        ]);
    }

    public function test_it_returns_error_when_deleting_a_non_existing_book(): void
    {
        $response = $this->deleteJson('/api/books/1');
        $response->assertStatus(404);
    }
}
