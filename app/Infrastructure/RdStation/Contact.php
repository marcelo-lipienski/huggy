<?php

declare(strict_types=1);

namespace App\Infrastructure\RdStation;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

final class Contact
{
    /** @var array<string, string> */
    private array $headers;

    private string $token;

    private string $endpoint;

    public function __construct(private Client $client)
    {
        $this->headers = [
            'accept' => 'application/json',
            'content-type' => 'application/json',
        ];

        /** @phpstan-ignore-next-line */
        $this->token = config('rd_station.crm.token');
        /** @phpstan-ignore-next-line */
        $this->endpoint = config('rd_station.crm.endpoint');
    }

    /** @param array<mixed> $body */
    public function create(array $body): ResponseInterface
    {
        return $this->request('POST', "{$this->endpoint}/contacts?token={$this->token}", $body);
    }

    /** @param array<mixed> $body */
    public function update(string $id, array $body): ResponseInterface
    {
        return $this->request('PUT', "{$this->endpoint}/contacts/{$id}?token={$this->token}", $body);
    }

    /** @param array<mixed> $body */
    private function request(string $method, string $resource, array $body): ResponseInterface
    {
        return $this->client->request($method, $resource, [
            'headers' => $this->headers,
            'body' => json_encode($body),
        ]);
    }
}
