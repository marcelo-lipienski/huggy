<?php

namespace App\Domain\Reader\Notifications;

use App\Models\Reader;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Cache;

class ReaderBirthday extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /** @return array<int, string> */
    public function via(): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(Reader $reader): MailMessage
    {
        /** @var array<string, int> $details */
        $details = Cache::get("reader:{$reader->id}");

        return (new MailMessage)
            ->greeting("Olá {$reader->name}")
            ->line('Parabéns pelo seu aniverśario!')
            ->line("Você leu {$details['books']} livro(s) com um total de {$details['pages']} páginas.");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
