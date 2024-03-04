<?php

declare(strict_types=1);

namespace App\Infrastructure\RdStation;

use App\Infrastructure\Interfaces\Api\ReaderContactInterface;
use App\Models\Reader;

final class ReaderContact implements ReaderContactInterface
{
    public static function from(Reader $reader): array
    {
        return [
            'contact' => [
                'birthday' => [
                    'day' => date_format($reader->birthdate, 'j'),
                    'month' => date_format($reader->birthdate, 'n'),
                    'year' => date_format($reader->birthdate, 'Y'),
                ],
                'emails' => [
                    [
                        'email' => $reader->email,
                    ],
                ],
                'name' => $reader->name,
            ],
        ];
    }
}
