<?php

namespace App\Http\Controllers\Guest;

use App\Events\TicketCreated;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\TicketLog;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TicketController extends Controller
{
    public function create(): View
    {
        return view('pages.tiket.lapor', [
            'title' => 'Lapor Kerusakan / Permintaan',
            'categories' => Category::orderBy('name')->get(),
            'units' => Unit::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'reporter_name' => ['required', 'string', 'max:255'],
            'reporter_phone' => ['nullable', 'string', 'max:20'],
            'unit_id' => ['required', 'exists:units,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'priority' => ['required', 'in:low,medium,high'],
            'photo' => ['nullable', 'image', 'max:4096'],
        ]);

        $ticket = DB::transaction(function () use ($data, $request) {
            $ticket = Ticket::create([
                ...collect($data)->except('photo')->all(),
                'ticket_code' => $this->generateTicketCode(),
                'status' => 'open',
            ]);

            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('tickets', 'public');
                $ticket->attachments()->create([
                    'type' => 'report',
                    'path' => $path,
                    'original_name' => $request->file('photo')->getClientOriginalName(),
                ]);
            }

            TicketLog::create([
                'ticket_id' => $ticket->id,
                'user_id' => null,
                'type' => 'status_change',
                'message' => 'Tiket dibuat oleh pelapor.',
                'old_status' => null,
                'new_status' => 'open',
            ]);

            return $ticket;
        });

        event(new TicketCreated($ticket));

        return redirect()->route('lapor.success', $ticket->ticket_code);
    }

    public function success(Ticket $ticket): View
    {
        return view('pages.tiket.lapor-sukses', [
            'title' => 'Tiket Berhasil Dibuat',
            'ticket' => $ticket,
        ]);
    }

    public function trackForm(): View
    {
        return view('pages.tiket.lacak', ['title' => 'Lacak Tiket']);
    }

    public function track(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ticket_code' => ['required', 'string'],
        ]);

        $ticket = Ticket::where('ticket_code', $data['ticket_code'])->first();

        if (! $ticket) {
            return back()->withErrors(['ticket_code' => 'Kode tiket tidak ditemukan.'])->withInput();
        }

        return redirect()->route('lacak.show', $ticket->ticket_code);
    }

    public function show(Ticket $ticket): View
    {
        return view('pages.tiket.lacak-detail', [
            'title' => 'Status Tiket',
            'ticket' => $ticket->load(['category', 'unit', 'assignee', 'logs', 'attachments']),
        ]);
    }

    private function generateTicketCode(): string
    {
        do {
            $code = 'TIK-'.now()->format('Ymd').'-'.str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        } while (Ticket::where('ticket_code', $code)->exists());

        return $code;
    }
}
