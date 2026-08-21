<?php

namespace App\Listeners;

use App\Events\TicketCompleted;
use App\Models\WhatsappSetting;
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
            WhatsappSetting::current()->renderTemplate('msg_ticket_completed', [
                'kode_tiket' => $ticket->ticket_code,
            ]),
            $ticket->id,
            'ticket_completed'
        );
    }
}
