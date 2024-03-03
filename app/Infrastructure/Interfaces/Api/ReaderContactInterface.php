<?php

declare(strict_types=1);

namespace App\Infrastructure\Interfaces\Api;

use App\Models\Reader;

interface ReaderContactInterface
{
    /** @return array<mixed> */
    public static function from(Reader $reader): array;
}
