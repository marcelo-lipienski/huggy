<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Reader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ReaderControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_an_empty_list_of_readers(): void
    {
        $response = $this->get('/api/readers');

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
}
