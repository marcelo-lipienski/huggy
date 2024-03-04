<?php

namespace Tests\Feature\Http\Controllers;

use App\Domain\Reader\Jobs\CreateContact;
use App\Domain\Reader\Jobs\UpdateContact;
use App\Models\Book;
use App\Models\Reader;
use App\Models\ReaderBook;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Testing\Fluent\AssertableJson;
use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ReaderControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        Queue::fake([CreateContact::class, UpdateContact::class]);
    }

    public function test_it_returns_an_empty_list_of_readers(): void
    {
        $response = $this->getJson('/api/readers');

        $response->assertStatus(200);
        $response->assertJson([]);
    }

    public function test_it_returns_a_list_of_readers_ordered_by_most_recent(): void
    {
        [$givenFirstReader, $givenSecondReader, $givenThirdReader] = Reader::factory()->count(3)->create();

        $response = $this->get('/api/readers');

        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) => $json->has('data')
            ->has('data.0', fn (AssertableJson $json) => $json
                ->where('id', $givenThirdReader->id)
                ->where('name', $givenThirdReader->name)
                ->where('phone_number', $givenThirdReader->phone_number)
                ->where('address', $givenThirdReader->address)
                ->where('birthdate', $givenThirdReader->birthdate->format('Y-m-d'))
                ->where('token', $givenThirdReader->token)
                ->etc()
            )
            ->has('data.1', fn (AssertableJson $json) => $json
                ->where('id', $givenSecondReader->id)
                ->where('name', $givenSecondReader->name)
                ->where('phone_number', $givenSecondReader->phone_number)
                ->where('address', $givenSecondReader->address)
                ->where('birthdate', $givenSecondReader->birthdate->format('Y-m-d'))
                ->where('token', $givenSecondReader->token)
                ->etc()
            )
            ->has('data.2', fn (AssertableJson $json) => $json
                ->where('id', $givenFirstReader->id)
                ->where('name', $givenFirstReader->name)
                ->where('phone_number', $givenFirstReader->phone_number)
                ->where('address', $givenFirstReader->address)
                ->where('birthdate', $givenFirstReader->birthdate->format('Y-m-d'))
                ->where('token', $givenFirstReader->token)
                ->etc()
            )
        );
    }

    public function test_it_returns_created_reader(): void
    {
        $givenAttributes = [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'phone_number' => '12345678901',
            'address' => fake()->address(),
            'birthdate' => fake()->date(),
        ];

        $response = $this->postJson('/api/readers', $givenAttributes);
        $response->assertStatus(201);
        $response->assertJson(fn (AssertableJson $json) => $json->has('data', fn (AssertableJson $json) => $json
            ->where('name', $givenAttributes['name'])
            ->where('phone_number', $givenAttributes['phone_number'])
            ->where('address', $givenAttributes['address'])
            ->where('birthdate', $givenAttributes['birthdate'])
            ->etc()
        )
        );

        $this->assertDatabaseCount(Reader::class, 1);

        Queue::assertPushed(CreateContact::class, 1);
    }

    public function test_it_fails_to_create_reader_with_an_already_used_email(): void
    {
        $givenAttributes = [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'phone_number' => '12345678901',
            'address' => fake()->address(),
            'birthdate' => fake()->date(),
        ];

        $response = $this->postJson('/api/readers', $givenAttributes);
        $response->assertStatus(201);
        $response->assertJson(fn (AssertableJson $json) => $json->has('data', fn (AssertableJson $json) => $json
            ->where('name', $givenAttributes['name'])
            ->where('phone_number', $givenAttributes['phone_number'])
            ->where('address', $givenAttributes['address'])
            ->where('birthdate', $givenAttributes['birthdate'])
            ->etc()
        )
        );

        Queue::assertPushed(CreateContact::class, 1);

        $response = $this->postJson('/api/readers', $givenAttributes);
        $response->assertStatus(422);

        $this->assertDatabaseCount(Reader::class, 1);

        // Still just called CreateContact once (for the first post)
        Queue::assertPushed(CreateContact::class, 1);
    }

    /**
     * @param  array<string, array<string, string>>  $givenAttributes
     */
    #[DataProvider('invalidReaderAttributesProvider')]
    public function test_it_returns_error_when_creating_reader_with_invalid_attributes(array $givenAttributes): void
    {
        $response = $this->postJson('/api/readers', $givenAttributes);
        $response->assertStatus(422);

        Queue::assertNotPushed(CreateContact::class);
    }

    public static function invalidReaderAttributesProvider(): Iterator
    {
        yield 'missing name' => [
            'givenAttributes' => [
                'name' => '',
                'email' => fake()->email(),
                'phone_number' => '12345678901',
                'address' => fake()->address(),
                'birthdate' => fake()->date(),
            ],
        ];

        yield 'missing email' => [
            'givenAttributes' => [
                'name' => fake()->name(),
                'email' => fake()->text(),
                'phone_number' => '12345678901',
                'address' => fake()->address(),
                'birthdate' => fake()->date(),
            ],
        ];

        yield 'invalid phone number' => [
            'givenAttributes' => [
                'name' => fake()->name(),
                'email' => fake()->email(),
                'phone_number' => '',
                'address' => fake()->address(),
                'birthdate' => fake()->date(),
            ],
        ];

        yield 'invalid address' => [
            'givenAttributes' => [
                'name' => fake()->name(),
                'email' => fake()->email(),
                'phone_number' => '12345678901',
                'address' => '',
                'birthdate' => fake()->date(),
            ],
        ];

        yield 'invalid birthdate' => [
            'givenAttributes' => [
                'name' => fake()->name(),
                'email' => fake()->email(),
                'phone_number' => '12345678901',
                'address' => fake()->address(),
                'birthdate' => fake()->text(),
            ],
        ];
    }

    public function test_it_returns_reader_by_id(): void
    {
        $givenReader = Reader::factory()->create();

        $response = $this->getJson("/api/readers/{$givenReader->id}");
        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) => $json->has('data', fn (AssertableJson $json) => $json
            ->where('id', $givenReader->id)
            ->where('name', $givenReader->name)
            ->where('phone_number', $givenReader->phone_number)
            ->where('address', $givenReader->address)
            ->where('birthdate', $givenReader->birthdate->format('Y-m-d'))
            ->etc()
        )
        );
    }

    public function test_it_returns_error_when_reader_does_not_exist(): void
    {
        $response = $this->getJson('/api/readers/1');
        $response->assertStatus(404);
    }

    public function test_it_updates_reader_by_id(): void
    {
        $givenReader = Reader::factory()->create();
        $givenReaderNewValues = [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'phone_number' => '10987654321',
        ];

        $response = $this->putJson("/api/readers/{$givenReader->id}", $givenReaderNewValues);
        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) => $json->has('data', fn (AssertableJson $json) => $json
            ->where('id', $givenReader->id)
            ->where('name', $givenReaderNewValues['name'])
            ->where('email', $givenReaderNewValues['email'])
            ->where('phone_number', $givenReaderNewValues['phone_number'])
            ->where('address', $givenReader->address)
            ->where('birthdate', $givenReader->birthdate->format('Y-m-d'))
            ->etc()
        )
        );

        $this->assertDatabaseHas(Reader::class, [
            'id' => $givenReader->id,
            'name' => $givenReaderNewValues['name'],
            'email' => $givenReaderNewValues['email'],
            'phone_number' => $givenReaderNewValues['phone_number'],
            'address' => $givenReader->address,
            'birthdate' => $givenReader->birthdate->format('Y-m-d'),
        ]);

        Queue::assertPushed(UpdateContact::class, 1);
    }

    public function test_it_returns_error_when_updating_a_non_existing_reader(): void
    {
        $givenReaderNewValues = [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'phone_number' => '10987654321',
            'address' => fake()->address(),
            'birthdate' => fake()->date(),
        ];

        $response = $this->putJson('/api/readers/1', $givenReaderNewValues);
        $response->assertStatus(404);

        Queue::assertNotPushed(UpdateContact::class);
    }

    public function test_it_deletes_reader_by_id(): void
    {
        $givenReader = Reader::factory()->create();

        $response = $this->deleteJson("/api/readers/{$givenReader->id}");
        $response->assertStatus(200);
        $this->assertDatabaseMissing(Reader::class, [
            'id' => $givenReader->id,
        ]);
    }

    public function test_it_returns_error_when_deleting_a_non_existing_reader(): void
    {
        $response = $this->deleteJson('/api/readers/1');
        $response->assertStatus(404);
    }

    public function test_it_mark_a_book_as_read_for_a_reader(): void
    {
        $givenReader = Reader::factory()->create();
        $givenBook = Book::factory()->create();

        Cache::shouldReceive('put')
            ->once()
            ->with("reader:{$givenReader->id}", [
                'books' => 1,
                'pages' => $givenBook->pages,
            ]);

        $response = $this->postJson("/api/readers/{$givenReader->id}/book/{$givenBook->id}", ['token' => $givenReader->token]);
        $response->assertStatus(200);

        $this->assertDatabaseHas(ReaderBook::class, [
            'reader_id' => $givenReader->id,
            'book_id' => $givenBook->id,
        ]);
    }

    public function test_it_has_read_several_books(): void
    {
        $givenReader = Reader::factory()->create();
        [$givenFirstBook, $givenSecondBook, $givenThirdBook] = Book::factory()->count(3)->create();

        Cache::shouldReceive('put')
            ->times(3);

        $this->postJson("/api/readers/{$givenReader->id}/book/{$givenFirstBook->id}", ['token' => $givenReader->token]);
        $this->postJson("/api/readers/{$givenReader->id}/book/{$givenSecondBook->id}", ['token' => $givenReader->token]);
        $this->postJson("/api/readers/{$givenReader->id}/book/{$givenThirdBook->id}", ['token' => $givenReader->token]);
    }

    public function test_it_fails_to_mark_a_book_as_read_for_a_reader_when_reader_does_not_exist(): void
    {
        $givenBook = Book::factory()->create();

        $response = $this->postJson("/api/readers/9999/book/{$givenBook->id}");
        $response->assertStatus(404);

        $this->assertDatabaseMissing(ReaderBook::class, [
            'reader_id' => 9999,
            'book_id' => $givenBook->id,
        ]);
    }

    public function test_it_fails_to_mark_a_book_as_read_for_a_reader_when_book_does_not_exist(): void
    {
        $givenReader = Reader::factory()->create();

        $response = $this->postJson("/api/readers/{$givenReader->id}/book/9999");
        $response->assertStatus(404);

        $this->assertDatabaseMissing(ReaderBook::class, [
            'reader_id' => $givenReader->id,
            'book_id' => 9999,
        ]);
    }

    public function test_it_fails_to_mark_a_book_as_read_for_a_reader_when_token_is_wrong(): void
    {
        $givenReader = Reader::factory()->create();
        $givenInvalidToken = 'invalid-token';

        $response = $this->postJson("/api/readers/{$givenReader->id}/book/9999", ['token' => $givenInvalidToken]);
        $response->assertStatus(404);
    }
}
