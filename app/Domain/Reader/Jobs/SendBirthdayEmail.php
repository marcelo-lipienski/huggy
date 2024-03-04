<?php

namespace App\Domain\Reader\Jobs;

use App\Domain\Reader\Notifications\ReaderBirthday;
use App\Models\Reader;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class SendBirthdayEmail implements ShouldQueue
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
    public function handle(): void
    {
        Notification::send(
            $this->reader,
            new ReaderBirthday()
        );
    }
}
