<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePublisherRequest;
use App\Http\Requests\UpdatePublisherRequest;
use App\Http\Resources\PublisherResource;
use App\Models\Publisher;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PublisherController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return PublisherResource::collection(
            Publisher::orderBy('id', 'desc')->get()
        );
    }

    public function store(StorePublisherRequest $request): PublisherResource
    {
        $publisher = Publisher::create($request->validated());

        return new PublisherResource($publisher);
    }

    public function show(int $id): JsonResponse|PublisherResource
    {
        try {
            return new PublisherResource(
                Publisher::findOrFail($id)
            );
        } catch (Exception $e) {
            return response()->json([], 404);
        }
    }

    public function update(UpdatePublisherRequest $request, int $id): JsonResponse|PublisherResource
    {
        try {
            $publisher = Publisher::findOrFail($id);
        } catch (Exception $e) {
            return response()->json([], 404);
        }

        foreach ($request->validated() as $attribute => $newValue) {
            $publisher->$attribute = $newValue;
        }

        $publisher->save();

        return new PublisherResource($publisher);
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $publisher = Publisher::findOrFail($id);
            $publisher->delete();

            return response()->json([], 200);
        } catch (Exception $e) {
            return response()->json([], 404);
        }
    }
}
