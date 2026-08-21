<?php

namespace App\Listeners;

use App\Events\TicketCompleted;
use App\Services\GowaService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendTicketCompletedNotification implements ShouldQueue
{
    public function __construct(private GowaService $gowa)
    {
    }

    public function handle(TicketCompleted $event): void
    {
        $ticket = $event->ticket;

        if (! $ticket->reporter_phone) {
            return;
        }

        $this->gowa->send(
            $ticket->reporter_phone,
            "Tiket #{$ticket->ticket_code} telah selesai dikerjakan.\n".
                "Terima kasih telah melaporkan melalui E-Tiket IT Rumah Sakit.",
            $ticket->id,
            'ticket_completed'
        );
    }
}
