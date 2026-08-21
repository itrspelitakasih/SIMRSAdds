<?php

namespace App\Http\Controllers\Admin;

use App\Events\TicketCompleted;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\TicketLog;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $query = Ticket::with(['category', 'unit', 'assignee'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }

        if ($request->filled('unit_id')) {
            $query->where('unit_id', $request->integer('unit_id'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->string('priority'));
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('ticket_code', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('reporter_name', 'like', "%{$search}%");
            });
        }

        return view('pages.admin.tickets.index', [
            'title' => 'Tiket',
            'tickets' => $query->paginate(15)->withQueryString(),
            'categories' => Category::orderBy('name')->get(),
            'units' => Unit::orderBy('name')->get(),
        ]);
    }

    public function show(Ticket $ticket): View
    {
        return view('pages.admin.tickets.show', [
            'title' => 'Detail Tiket',
            'ticket' => $ticket->load(['category', 'unit', 'assignee', 'attachments', 'logs.user']),
            'technicians' => User::orderBy('name')->get(),
        ]);
    }

    public function assign(Request $request, Ticket $ticket): RedirectResponse
    {
        $data = $request->validate([
            'assigned_to' => ['nullable', 'exists:users,id'],
        ]);

        $ticket->update(['assigned_to' => $data['assigned_to'] ?? null]);

        $assignee = $ticket->assignee()->first();

        TicketLog::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'type' => 'comment',
            'message' => $assignee
                ? "Tiket ditugaskan ke {$assignee->name}."
                : 'Penugasan tiket dibatalkan.',
        ]);

        return back()->with('status', 'Tiket berhasil ditugaskan.');
    }

    public function updateStatus(Request $request, Ticket $ticket): RedirectResponse
    {
        $becomingDone = $request->input('status') === 'done' && $ticket->status !== 'done';
        $needsProof = $becomingDone && $ticket->completionAttachments()->doesntExist();

        $data = $request->validate([
            'status' => ['required', 'in:open,in_progress,waiting_confirmation,done,rejected'],
            'completion_proof' => [Rule::requiredIf($needsProof), 'array'],
            'completion_proof.*' => ['image', 'max:4096'],
        ], [
            'completion_proof.required' => 'Unggah minimal 1 foto bukti dukung sebelum menandai tiket selesai.',
        ]);

        $oldStatus = $ticket->status;
        $newStatus = $data['status'];

        $ticket->status = $newStatus;
        if ($newStatus === 'in_progress' && $ticket->started_at === null) {
            $ticket->started_at = now();
        }
        if ($newStatus === 'done' && $oldStatus !== 'done') {
            $ticket->resolved_at = now();
        }
        $ticket->save();

        foreach ($request->file('completion_proof', []) as $file) {
            $path = $file->store('tickets/completion', 'public');
            $ticket->attachments()->create([
                'type' => 'completion',
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
            ]);
        }

        TicketLog::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'type' => 'status_change',
            'message' => 'Status diubah dari '.(Ticket::STATUSES[$oldStatus] ?? $oldStatus).' menjadi '.(Ticket::STATUSES[$newStatus] ?? $newStatus).'.',
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
        ]);

        if ($newStatus === 'done' && $oldStatus !== 'done') {
            event(new TicketCompleted($ticket));
        }

        return back()->with('status', 'Status tiket berhasil diperbarui.');
    }

    public function addComment(Request $request, Ticket $ticket): RedirectResponse
    {
        $data = $request->validate([
            'message' => ['required', 'string'],
        ]);

        TicketLog::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'type' => 'comment',
            'message' => $data['message'],
        ]);

        return back()->with('status', 'Komentar ditambahkan.');
    }
}
