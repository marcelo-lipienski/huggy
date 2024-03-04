<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\RdStation;

use App\Infrastructure\RdStation\Contact;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Tests\TestCase;

class ContactTest extends TestCase
{
    private Contact $givenContactApi;

    public function setUp(): void
    {
        parent::setUp();

        $mock = new MockHandler([
            new Response(200),
        ]);

        $handlerStack = HandlerStack::create($mock);

        $givenGuzzleClient = new Client(['handler' => $handlerStack]);

        $this->givenContactApi = new Contact($givenGuzzleClient);
    }

    public function test_it_create_a_contact(): void
    {
        $response = $this->givenContactApi->create($givenBody = []);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    public function test_it_update_a_contact(): void
    {
        $response = $this->givenContactApi->update($givenId = 'abc', $givenBody = []);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }
}
