<?php

namespace App\Listeners;

use App\Events\TicketCreated;
use App\Models\WhatsappSetting;
use App\Services\GowaService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendTicketCreatedNotification implements ShouldQueue
{
    public function __construct(private GowaService $gowa) {}

    public function handle(TicketCreated $event): void
    {
        $ticket = $event->ticket->loadMissing(['category', 'unit']);

        $adminNumber = WhatsappSetting::current()->notify_admin_number;

        if ($adminNumber) {
            $this->gowa->send(
                $adminNumber,
                "🎫 *Tiket Baru Masuk*\n\n" .
                    "*Kode Tiket:* {$ticket->ticket_code}\n" .
                    "*Pelapor:* {$ticket->reporter_name}\n" .
                    "*Unit:* {$ticket->unit->name}\n" .
                    "*Kategori:* {$ticket->category->name}\n" .
                    "*Judul:* {$ticket->title}\n\n" .
                    'Mohon segera ditindaklanjuti.',
                $ticket->id,
                'ticket_created'
            );
        }

        if ($ticket->reporter_phone) {
            $this->gowa->send(
                $ticket->reporter_phone,
                "✅ *Tiket Anda Telah Diterima*\n\n" .
                    "*Kode Tiket:* {$ticket->ticket_code}\n\n" .
                    'Silakan simpan kode ini untuk melacak status tiket.',
                $ticket->id,
                'ticket_created'
            );
        }
    }
}
