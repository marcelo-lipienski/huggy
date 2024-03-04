<?php

use App\Models\Book;
use App\Models\Reader;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('readers_books', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Reader::class);
            $table->foreignIdFor(Book::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('readers_books');
    }
};
