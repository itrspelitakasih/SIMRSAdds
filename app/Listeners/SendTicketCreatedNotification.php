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

        $setting = WhatsappSetting::current();

        if ($setting->notify_admin_number) {
            $this->gowa->send(
                $setting->notify_admin_number,
                $setting->renderTemplate('msg_ticket_created_admin', [
                    'kode_tiket' => $ticket->ticket_code,
                    'pelapor' => $ticket->reporter_name,
                    'unit' => $ticket->unit->name,
                    'kategori' => $ticket->category->name,
                    'judul' => $ticket->title,
                ]),
                $ticket->id,
                'ticket_created'
            );
        }

        if ($ticket->reporter_phone) {
            $this->gowa->send(
                $ticket->reporter_phone,
                $setting->renderTemplate('msg_ticket_created_reporter', [
                    'kode_tiket' => $ticket->ticket_code,
                ]),
                $ticket->id,
                'ticket_created'
            );
        }
    }
}
