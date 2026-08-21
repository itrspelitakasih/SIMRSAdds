<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Tiket</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #1f2937; }
        h1 { font-size: 16px; margin-bottom: 4px; }
        p.sub { margin-top: 0; color: #6b7280; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #e5e7eb; padding: 6px 8px; text-align: left; }
        th { background: #f3f4f6; }
    </style>
</head>
<body>
    <h1>Laporan Tiket E-Tiket IT Rumah Sakit</h1>
    <p class="sub">Periode: {{ $filters['month'] }}</p>

    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Judul</th>
                <th>Pelapor</th>
                <th>Unit</th>
                <th>Kategori</th>
                <th>Prioritas</th>
                <th>Status</th>
                <th>Teknisi</th>
                <th>Tanggal Lapor</th>
                <th>Tanggal Mulai</th>
                <th>Tanggal Selesai</th>
                <th>Bukti Dukung</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($tickets as $ticket)
                <tr>
                    <td>{{ $ticket->ticket_code }}</td>
                    <td>{{ $ticket->title }}</td>
                    <td>{{ $ticket->reporter_name }}</td>
                    <td>{{ $ticket->unit->name }}</td>
                    <td>{{ $ticket->category->name }}</td>
                    <td>{{ $ticket->priorityLabel() }}</td>
                    <td>{{ $ticket->statusLabel() }}</td>
                    <td>{{ $ticket->assignee->name ?? '-' }}</td>
                    <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $ticket->started_at?->format('d/m/Y H:i') ?? '-' }}</td>
                    <td>{{ $ticket->resolved_at?->format('d/m/Y H:i') ?? '-' }}</td>
                    <td>{{ $ticket->completionAttachments->count() ? $ticket->completionAttachments->count().' foto' : '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="12">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
