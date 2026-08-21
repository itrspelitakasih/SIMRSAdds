<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Document;
use App\Models\Ticket;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $startOfMonth = now()->startOfMonth();
        $startOfLastMonth = now()->subMonthNoOverflow()->startOfMonth();
        $endOfLastMonth = now()->subMonthNoOverflow()->endOfMonth();

        $totalThisMonth = Ticket::where('created_at', '>=', $startOfMonth)->count();
        $openCount = Ticket::whereIn('status', ['open', 'in_progress', 'waiting_confirmation'])->count();
        $doneThisMonth = Ticket::where('status', 'done')->where('resolved_at', '>=', $startOfMonth)->count();
        $rejectedThisMonth = Ticket::where('status', 'rejected')->where('created_at', '>=', $startOfMonth)->count();
        $resolvedToday = Ticket::where('status', 'done')->whereDate('resolved_at', now()->toDateString())->count();

        $incomingLastMonth = Ticket::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();
        $doneLastMonth = Ticket::where('status', 'done')
            ->whereBetween('resolved_at', [$startOfLastMonth, $endOfLastMonth])
            ->count();
        $rejectedLastMonth = Ticket::where('status', 'rejected')
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->count();

        $percentChange = function (int $current, int $previous): float {
            if ($previous === 0) {
                return $current > 0 ? 100.0 : 0.0;
            }

            return round((($current - $previous) / $previous) * 100, 1);
        };

        $incomingChange = $percentChange($totalThisMonth, $incomingLastMonth);
        $doneChange = $percentChange($doneThisMonth, $doneLastMonth);
        $rejectedChange = $percentChange($rejectedThisMonth, $rejectedLastMonth);

        $resolutionRate = $totalThisMonth > 0 ? round(($doneThisMonth / $totalThisMonth) * 100, 1) : 0.0;

        $avgResolutionHours = Ticket::whereNotNull('resolved_at')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours'))
            ->value('avg_hours');

        $statusCounts = Ticket::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $categoryCounts = Category::withCount('tickets')->orderByDesc('tickets_count')->get();

        $monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        $monthlyRaw = Ticket::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->pluck('total', 'month');

        $monthlyTicketChart = [
            'labels' => $monthLabels,
            'series' => collect(range(1, 12))->map(fn ($month) => (int) ($monthlyRaw[$month] ?? 0))->values(),
        ];

        $unitTicketCounts = Ticket::selectRaw("unit_id, COUNT(*) as total, SUM(CASE WHEN status = 'done' THEN 1 ELSE 0 END) as completed_total")
            ->groupBy('unit_id')
            ->get()
            ->keyBy('unit_id');

        $unitsForStatisticsChart = Unit::whereIn('id', $unitTicketCounts->keys())->orderBy('name')->get();

        $ticketStatisticsChart = [
            'labels' => $unitsForStatisticsChart->pluck('name')->values(),
            'incoming' => $unitsForStatisticsChart->map(fn (Unit $unit) => (int) ($unitTicketCounts[$unit->id]->total ?? 0))->values(),
            'completed' => $unitsForStatisticsChart->map(fn (Unit $unit) => (int) ($unitTicketCounts[$unit->id]->completed_total ?? 0))->values(),
        ];

        $totalTicketsAll = Ticket::count();
        $categoryBreakdown = $categoryCounts->take(5)->map(fn (Category $category) => [
            'name' => $category->name,
            'count' => $category->tickets_count,
            'percentage' => $totalTicketsAll > 0 ? (int) round($category->tickets_count / $totalTicketsAll * 100) : 0,
        ])->values();

        $unitCategoryCounts = Ticket::selectRaw('unit_id, category_id, count(*) as total')
            ->groupBy('unit_id', 'category_id')
            ->get()
            ->groupBy('unit_id');

        $unitsWithTickets = Unit::whereIn('id', $unitCategoryCounts->keys())->orderBy('name')->get();
        $categoriesForChart = Category::orderBy('name')->get();

        $categoryByUnitChart = [
            'units' => $unitsWithTickets->pluck('name')->values(),
            'series' => $categoriesForChart->map(fn (Category $category) => [
                'name' => $category->name,
                'data' => $unitsWithTickets->map(function (Unit $unit) use ($category, $unitCategoryCounts) {
                    $row = $unitCategoryCounts->get($unit->id, collect())->firstWhere('category_id', $category->id);

                    return $row ? (int) $row->total : 0;
                })->values(),
            ])->values(),
        ];

        $recentTickets = Ticket::with(['category', 'unit'])->latest()->limit(8)->get();

        $documentStatusColors = [
            'expired' => 'Danger',
            'critical' => 'Danger',
            'warning' => 'Warning',
            'ok' => 'Success',
            'none' => 'Primary',
        ];

        $documentReminderEvents = [];

        if (auth()->user()->can('documents')) {
            $documentReminderEvents = Document::query()
                ->whereNotNull('expiry_date')
                ->where('is_active', true)
                ->get()
                ->map(fn (Document $document) => [
                    'id' => 'document-reminder-'.$document->id,
                    'title' => 'Jatuh Tempo: '.$document->title,
                    'start' => $document->expiry_date->toDateString(),
                    'allDay' => true,
                    'url' => route('documents.edit', $document),
                    'extendedProps' => ['calendar' => $documentStatusColors[$document->expiryStatus()] ?? 'Primary'],
                ])
                ->values()
                ->all();
        }

        return view('pages.admin.dashboard', [
            'title' => 'Dashboard',
            'totalThisMonth' => $totalThisMonth,
            'openCount' => $openCount,
            'doneThisMonth' => $doneThisMonth,
            'rejectedThisMonth' => $rejectedThisMonth,
            'resolvedToday' => $resolvedToday,
            'incomingChange' => $incomingChange,
            'doneChange' => $doneChange,
            'rejectedChange' => $rejectedChange,
            'resolutionRate' => $resolutionRate,
            'avgResolutionHours' => $avgResolutionHours ? round($avgResolutionHours, 1) : null,
            'statusCounts' => $statusCounts,
            'categoryCounts' => $categoryCounts,
            'categoryBreakdown' => $categoryBreakdown,
            'monthlyTicketChart' => $monthlyTicketChart,
            'ticketStatisticsChart' => $ticketStatisticsChart,
            'categoryByUnitChart' => $categoryByUnitChart,
            'recentTickets' => $recentTickets,
            'documentReminderEvents' => $documentReminderEvents,
        ]);
    }
}
