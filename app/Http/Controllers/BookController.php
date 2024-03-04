<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BookController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return BookResource::collection(
            Book::orderBy('id', 'desc')->get()
        );
    }

    public function store(StoreBookRequest $request): BookResource
    {
        $book = Book::create($request->validated());

        return new BookResource($book);
    }

    public function show(int $id): JsonResponse|BookResource
    {
        try {
            return new BookResource(
                Book::findOrFail($id)
            );
        } catch (Exception $e) {
            return response()->json([], 404);
        }
    }

    public function update(UpdateBookRequest $request, int $id): JsonResponse|BookResource
    {
        try {
            $book = Book::findOrFail($id);
        } catch (Exception $e) {
            return response()->json([], 404);
        }

        foreach ($request->validated() as $attribute => $newValue) {
            $book->$attribute = $newValue;
        }

        $book->save();

        return new BookResource($book);
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $book = Book::findOrFail($id);
            $book->delete();

            return response()->json([], 200);
        } catch (Exception $e) {
            return response()->json([], 404);
        }
    }
}
