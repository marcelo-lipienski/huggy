<?php

namespace App\Domain\Reader\Jobs;

use App\Infrastructure\Interfaces\Api\ContactInterface;
use App\Infrastructure\RdStation\ReaderContact;
use App\Models\Reader;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateContact implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private Reader $reader)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(ContactInterface $api): void
    {
        $response = $api->create(
            ReaderContact::from($this->reader)
        );

        /** @var \stdClass $contact */
        $contact = json_decode($response->getBody());

        $this->reader->crm_id = $contact->id;
        $this->reader->save();
    }
}
