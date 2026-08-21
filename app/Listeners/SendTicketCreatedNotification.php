<?php

namespace App\Listeners;

use App\Events\TicketCreated;
use App\Models\WhatsappSetting;
use App\Services\GowaService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendTicketCreatedNotification implements ShouldQueue
{
    public function __construct(private GowaService $gowa)
    {
    }

    public function handle(TicketCreated $event): void
    {
        $ticket = $event->ticket->loadMissing(['category', 'unit']);

        $adminNumber = WhatsappSetting::current()->notify_admin_number;

        if ($adminNumber) {
            $this->gowa->send(
                $adminNumber,
                "Tiket baru #{$ticket->ticket_code}\n".
                    "Pelapor: {$ticket->reporter_name} ({$ticket->unit->name})\n".
                    "Kategori: {$ticket->category->name}\n".
                    "Judul: {$ticket->title}",
                $ticket->id,
                'ticket_created'
            );
        }

        if ($ticket->reporter_phone) {
            $this->gowa->send(
                $ticket->reporter_phone,
                "Tiket Anda telah diterima.\n".
                    "Kode tiket: {$ticket->ticket_code}\n".
                    "Silakan simpan kode ini untuk melacak status perbaikan.",
                $ticket->id,
                'ticket_created'
            );
        }
    }
}
