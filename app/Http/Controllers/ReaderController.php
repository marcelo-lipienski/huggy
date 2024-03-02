<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReaderRequest;
use App\Http\Requests\UpdateReaderRequest;
use App\Http\Resources\ReaderResource;
use App\Models\Reader;
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
        $reader = Reader::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone_number' => $request->input('phone_number'),
            'address' => $request->input('address'),
            'birthdate' => $request->input('birthdate'),
        ]);

        return new ReaderResource($reader);
    }

    public function show(Reader $reader): void
    {
        //
    }

    public function update(UpdateReaderRequest $request, Reader $reader): void
    {
        //
    }

    public function destroy(Reader $reader): void
    {
        //
    }
}
