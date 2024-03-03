<?php

declare(strict_types=1);

namespace App\Infrastructure\Interfaces\Api;

use Psr\Http\Message\ResponseInterface;

interface ContactInterface
{
    /** @param array<mixed> $body */
    public function create(array $body): ResponseInterface;

    /** @param array<mixed> $body */
    public function update(string $id, array $body): ResponseInterface;
}
