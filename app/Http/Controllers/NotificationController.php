<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function tickets(Request $request): JsonResponse
    {
        if (! $request->user()->can('tickets')) {
            return response()->json(['count' => 0, 'tickets' => []]);
        }

        $openTickets = Ticket::with(['unit'])
            ->where('status', 'open')
            ->latest()
            ->limit(8)
            ->get();

        return response()->json([
            'count' => Ticket::where('status', 'open')->count(),
            'tickets' => $openTickets->map(fn (Ticket $ticket) => [
                'id' => $ticket->id,
                'ticket_code' => $ticket->ticket_code,
                'title' => $ticket->title,
                'reporter_name' => $ticket->reporter_name,
                'unit' => $ticket->unit?->name,
                'priority_label' => $ticket->priorityLabel(),
                'time' => $ticket->created_at->diffForHumans(),
                'url' => route('tickets.show', $ticket),
            ]),
        ]);
    }
}
