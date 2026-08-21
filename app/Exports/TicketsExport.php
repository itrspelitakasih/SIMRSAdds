<?php

namespace App\Exports;

use App\Models\Ticket;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TicketsExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private array $filters)
    {
    }

    public function collection(): Collection
    {
        return app(\App\Http\Controllers\Admin\ReportController::class)
            ->filteredTicketsQuery($this->filters)
            ->get();
    }

    public function headings(): array
    {
        return [
            'Kode Tiket', 'Judul', 'Pelapor', 'Unit', 'Kategori',
            'Prioritas', 'Status', 'Teknisi', 'Tanggal Lapor', 'Tanggal Mulai',
            'Tanggal Selesai', 'Bukti Dukung',
        ];
    }

    public function map($ticket): array
    {
        return [
            $ticket->ticket_code,
            $ticket->title,
            $ticket->reporter_name,
            $ticket->unit->name,
            $ticket->category->name,
            $ticket->priorityLabel(),
            $ticket->statusLabel(),
            $ticket->assignee->name ?? '-',
            $ticket->created_at->format('d/m/Y H:i'),
            $ticket->started_at?->format('d/m/Y H:i') ?? '-',
            $ticket->resolved_at?->format('d/m/Y H:i') ?? '-',
            $ticket->completionAttachments->isEmpty()
                ? '-'
                : $ticket->completionAttachments->map(fn ($a) => asset('storage/'.$a->path))->implode(' | '),
        ];
    }
}
