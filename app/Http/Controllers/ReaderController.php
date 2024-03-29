<?php

namespace App\Http\Controllers;

use App\Domain\Reader\Jobs\CreateContact;
use App\Domain\Reader\Jobs\UpdateContact;
use App\Domain\Reader\Services\MarkBookAsRead;
use App\Http\Requests\StoreReaderRequest;
use App\Http\Requests\UpdateReaderRequest;
use App\Http\Resources\ReaderResource;
use App\Models\Book;
use App\Models\Reader;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReaderController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return ReaderResource::collection(
            Reader::orderBy('id', 'desc')->get()
        );
    }

    public function store(StoreReaderRequest $request): ReaderResource
    {
        $reader = Reader::create($request->validated());

        CreateContact::dispatch($reader);

        return new ReaderResource($reader);
    }

    public function show(int $id): JsonResponse|ReaderResource
    {
        try {
            return new ReaderResource(
                Reader::findOrFail($id)
            );
        } catch (Exception $e) {
            return response()->json([], 404);
        }
    }

    public function update(UpdateReaderRequest $request, int $id): JsonResponse|ReaderResource
    {
        try {
            $reader = Reader::findOrFail($id);
        } catch (Exception $e) {
            return response()->json([], 404);
        }

        foreach ($request->validated() as $attribute => $newValue) {
            $reader->$attribute = $newValue;
        }

        $reader->save();

        UpdateContact::dispatch($reader);

        return new ReaderResource($reader);
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $reader = Reader::findOrFail($id);
            $reader->delete();

            return response()->json([], 200);
        } catch (Exception $e) {
            return response()->json([], 404);
        }
    }

    public function markAsRead(int $id, int $bookId): JsonResponse
    {
        try {
            $reader = Reader::findOrFail($id);
            $book = Book::findOrFail($bookId);

            $markBookAsRead = new MarkBookAsRead($reader, $book);
            $markBookAsRead->execute();

            return response()->json([], 200);
        } catch (Exception $e) {
            return response()->json([], 404);
        }
    }
}
