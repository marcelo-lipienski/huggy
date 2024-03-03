<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Publisher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PublisherControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_an_empty_list_of_publishers(): void
    {
        $response = $this->getJson('/api/publishers');

        $response->assertStatus(200);
        $response->assertJson([]);
    }

    public function test_it_returns_a_list_of_publishers_ordered_by_most_recent(): void
    {
        [$givenFirstPublisher, $givenSecondPublisher, $givenThirdPublisher] = Publisher::factory()->count(3)->create();

        $response = $this->get('/api/publishers');

        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) => $json->has('data')
            ->has('data.0', fn (AssertableJson $json) => $json
                ->where('id', $givenThirdPublisher->id)
                ->where('name', $givenThirdPublisher->name)
                ->where('code', $givenThirdPublisher->code)
                ->where('phone_number', $givenThirdPublisher->phone_number)
                ->etc()
            )
            ->has('data.1', fn (AssertableJson $json) => $json
                ->where('id', $givenSecondPublisher->id)
                ->where('name', $givenSecondPublisher->name)
                ->where('code', $givenSecondPublisher->code)
                ->where('phone_number', $givenSecondPublisher->phone_number)
                ->etc()
            )
            ->has('data.2', fn (AssertableJson $json) => $json
                ->where('id', $givenFirstPublisher->id)
                ->where('name', $givenFirstPublisher->name)
                ->where('code', $givenFirstPublisher->code)
                ->where('phone_number', $givenFirstPublisher->phone_number)
                ->etc()
            )
        );
    }

    public function test_it_returns_created_publisher(): void
    {
        $givenAttributes = [
            'name' => fake()->name(),
            'code' => (string) fake()->numberBetween(1000, 9999),
            'phone_number' => '12345678901',
        ];

        $response = $this->postJson('/api/publishers', $givenAttributes);
        $response->assertStatus(201);
        $response->assertJson(fn (AssertableJson $json) => $json->has('data', fn (AssertableJson $json) => $json
            ->where('name', $givenAttributes['name'])
            ->where('code', $givenAttributes['code'])
            ->where('phone_number', $givenAttributes['phone_number'])
            ->etc()
        )
        );

        $this->assertDatabaseCount(Publisher::class, 1);
    }

    public function test_it_fails_to_create_publisher_with_an_already_used_code(): void
    {
        $givenAttributes = [
            'name' => fake()->name(),
            'code' => (string) fake()->numberBetween(1000, 9999),
            'phone_number' => '12345678901',
        ];

        $response = $this->postJson('/api/publishers', $givenAttributes);
        $response->assertStatus(201);
        $response->assertJson(fn (AssertableJson $json) => $json->has('data', fn (AssertableJson $json) => $json
            ->where('name', $givenAttributes['name'])
            ->where('code', (string) $givenAttributes['code'])
            ->where('phone_number', $givenAttributes['phone_number'])
            ->etc()
        )
        );

        $response = $this->postJson('/api/publishers', $givenAttributes);
        $response->assertStatus(422);

        $this->assertDatabaseCount(Publisher::class, 1);
    }

    /**
     * @param  array<string, array<string, string>>  $givenAttributes
     */
    #[DataProvider('invalidPublisherAttributesProvider')]
    public function test_it_returns_error_when_creating_publisher_with_invalid_attributes(array $givenAttributes): void
    {
        $response = $this->postJson('/api/publishers', $givenAttributes);
        $response->assertStatus(422);
    }

    public static function invalidPublisherAttributesProvider(): Iterator
    {
        yield 'missing name' => [
            'givenAttributes' => [
                'name' => '',
                'code' => (string) fake()->numberBetween(1000, 9999),
                'phone_number' => '12345678901',
            ],
        ];

        yield 'missing code' => [
            'givenAttributes' => [
                'name' => fake()->name(),
                'phone_number' => '12345678901',
            ],
        ];

        yield 'invalid phone number' => [
            'givenAttributes' => [
                'name' => fake()->name(),
                'code' => (string) fake()->numberBetween(1000, 9999),
                'phone_number' => fake()->text(),
            ],
        ];
    }

    public function test_it_returns_publisher_by_id(): void
    {
        $givenPublisher = Publisher::factory()->create();

        $response = $this->getJson("/api/publishers/{$givenPublisher->id}");
        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) => $json->has('data', fn (AssertableJson $json) => $json
            ->where('id', $givenPublisher->id)
            ->where('name', $givenPublisher->name)
            ->where('code', $givenPublisher->code)
            ->where('phone_number', $givenPublisher->phone_number)
            ->etc()
        )
        );
    }

    public function test_it_returns_error_when_publisher_does_not_exist(): void
    {
        $response = $this->getJson('/api/publishers/1');
        $response->assertStatus(404);
    }

    public function test_it_updates_publisher_by_id(): void
    {
        $givenPublisher = Publisher::factory()->create();
        $givenPublisherNewValues = [
            'name' => fake()->name(),
            'code' => (string) fake()->numberBetween(1000, 9999),
            'phone_number' => '10987654321',
        ];

        $response = $this->putJson("/api/publishers/{$givenPublisher->id}", $givenPublisherNewValues);
        $response->assertStatus(200);
        $response->assertJson(fn (AssertableJson $json) => $json->has('data', fn (AssertableJson $json) => $json
            ->where('id', $givenPublisher->id)
            ->where('name', $givenPublisherNewValues['name'])
            ->where('code', $givenPublisherNewValues['code'])
            ->where('phone_number', $givenPublisherNewValues['phone_number'])
            ->etc()
        )
        );

        $this->assertDatabaseHas(Publisher::class, [
            'id' => $givenPublisher->id,
            'name' => $givenPublisherNewValues['name'],
            'code' => $givenPublisherNewValues['code'],
            'phone_number' => $givenPublisherNewValues['phone_number'],
        ]);
    }

    public function test_it_returns_error_when_updating_a_non_existing_publisher(): void
    {
        $givenPublisherNewValues = [
            'name' => fake()->name(),
            'code' => (string) fake()->numberBetween(1000, 9999),
            'phone_number' => '10987654321',
        ];

        $response = $this->putJson('/api/publishers/1', $givenPublisherNewValues);
        $response->assertStatus(404);
    }

    public function test_it_deletes_publisher_by_id(): void
    {
        $givenPublisher = Publisher::factory()->create();

        $response = $this->deleteJson("/api/publishers/{$givenPublisher->id}");
        $response->assertStatus(200);
        $this->assertDatabaseMissing(Publisher::class, [
            'id' => $givenPublisher->id,
        ]);
    }

    public function test_it_returns_error_when_deleting_a_non_existing_publisher(): void
    {
        $response = $this->deleteJson('/api/publishers/1');
        $response->assertStatus(404);
    }
}
