<?php

namespace App\Http\Controllers\Admin;

use App\Exports\TicketsExport;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\Unit;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->filtersFromRequest($request);

        $tickets = $this->filteredTicketsQuery($filters)->latest()->get();

        $summaryByCategory = $tickets->groupBy(fn (Ticket $t) => $t->category->name)
            ->map(fn ($group) => [
                'total' => $group->count(),
                'done' => $group->where('status', 'done')->count(),
            ]);

        return view('pages.admin.laporan.index', [
            'title' => 'Laporan',
            'tickets' => $tickets,
            'summaryByCategory' => $summaryByCategory,
            'categories' => Category::orderBy('name')->get(),
            'units' => Unit::orderBy('name')->get(),
            'filters' => $filters,
        ]);
    }

    public function export(Request $request): Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $filters = $this->filtersFromRequest($request);
        $format = $request->string('format', 'excel')->toString();

        $filename = 'laporan-tiket-'.now()->format('Ymd-His');

        if ($format === 'pdf') {
            $tickets = $this->filteredTicketsQuery($filters)->latest()->get();

            $pdf = Pdf::loadView('pages.admin.laporan.pdf', ['tickets' => $tickets, 'filters' => $filters]);

            return $pdf->download($filename.'.pdf');
        }

        return Excel::download(new TicketsExport($filters), $filename.'.xlsx');
    }

    public function filteredTicketsQuery(array $filters): Builder
    {
        $query = Ticket::with(['category', 'unit', 'assignee', 'completionAttachments']);

        if (! empty($filters['month']) && preg_match('/^\d{4}-\d{2}$/', $filters['month'])) {
            [$year, $month] = explode('-', $filters['month']);
            $query->whereYear('created_at', (int) $year)->whereMonth('created_at', (int) $month);
        }

        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (! empty($filters['unit_id'])) {
            $query->where('unit_id', $filters['unit_id']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query;
    }

    private function filtersFromRequest(Request $request): array
    {
        return [
            'month' => $request->string('month', now()->format('Y-m'))->toString(),
            'category_id' => $request->input('category_id'),
            'unit_id' => $request->input('unit_id'),
            'status' => $request->input('status'),
        ];
    }
}
