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

class UpdateContact implements ShouldQueue
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
        if ($this->reader->crm_id == null) {
            return;
        }

        $api->update(
            $this->reader->crm_id,
            ReaderContact::from($this->reader)
        );
    }
}
