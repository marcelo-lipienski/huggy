<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Publisher;
use App\Models\Reader;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $readers = Reader::factory()
            ->count(5)
            ->create()
            ->toArray();

        $publishers = Publisher::factory()
            ->count(3)
            ->create();

        $books = Book::factory()
            ->count(10)
            ->recycle($publishers)
            ->create();

        foreach ($books as $book) {
            $randomReader = $readers[array_rand($readers)];

            $book->readers()->attach($randomReader['id']);
        }
    }
}
