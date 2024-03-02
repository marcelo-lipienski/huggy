<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Reader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ReaderControllerTest extends TestCase
{
    use RefreshDatabase;

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
                ->where('birthdate', $givenThirdReader->birthdate)
                ->etc()
            )
            ->has('data.1', fn (AssertableJson $json) => $json
                ->where('id', $givenSecondReader->id)
                ->where('name', $givenSecondReader->name)
                ->where('phone_number', $givenSecondReader->phone_number)
                ->where('address', $givenSecondReader->address)
                ->where('birthdate', $givenSecondReader->birthdate)
                ->etc()
            )
            ->has('data.2', fn (AssertableJson $json) => $json
                ->where('id', $givenFirstReader->id)
                ->where('name', $givenFirstReader->name)
                ->where('phone_number', $givenFirstReader->phone_number)
                ->where('address', $givenFirstReader->address)
                ->where('birthdate', $givenFirstReader->birthdate)
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
    }

    /**
     * @param array<string, array<string, string>> $givenAttributes
     */
    #[DataProvider('invalidReaderAttributesProvider')]
    public function test_it_returns_error_when_creating_reader_with_invalid_attributes(array $givenAttributes): void
    {
        $response = $this->postJson('/api/readers', $givenAttributes);
        $response->assertStatus(422);
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
                'phone_number' => fake()->text(),
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
}
