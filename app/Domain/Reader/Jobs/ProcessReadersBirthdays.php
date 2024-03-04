<?php

namespace App\Domain\Reader\Jobs;

use App\Models\Reader;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessReadersBirthdays implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $readers = Reader::whereMonth('birthdate', date('m'))
            ->whereDay('birthdate', date('d'))
            ->get();

        foreach ($readers as $reader) {
            SendBirthdayEmail::dispatch($reader);
        }
    }
}
