<?php

namespace App\Http\Resources;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Book
 */
class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'publisher_id' => $this->publisher_id,
            'title' => $this->title,
            'genre' => $this->genre,
            'author' => $this->author,
            'year' => $this->year,
            'pages' => $this->pages,
            'language' => $this->language,
            'edition' => $this->edition,
            'isbn' => $this->isbn,
        ];
    }
}
