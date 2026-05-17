<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{DocumentRequest, Report, Resident, SystemSetting};
use App\Services\AuditService;
use App\Services\PdfService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $recent = Report::with('generatedByUser')->latest()->limit(20)->get();
        $favorites = json_decode((string) SystemSetting::get('favorite_report_configs', '[]'), true) ?: [];

        return view('admin.reports.index', compact('recent', 'favorites'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'type' => 'required|in:daily,weekly,monthly,requests_summary,residents',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'format' => 'required|in:pdf,csv',
            'columns' => 'nullable|array',
            'columns.*' => 'string|max:80',
            'favorite_name' => 'nullable|string|max:100',
        ]);

        $filters = $request->only(['type', 'date_from', 'date_to', 'columns']);
        $from = $request->date_from ?? now()->startOfMonth()->toDateString();
        $to = $request->date_to ?? now()->toDateString();
        $columns = $request->input('columns', []);

        $report = Report::create([
            'report_name' => ucfirst(str_replace('_', ' ', $request->type)) . ' ' . now()->format('Y-m-d H:i'),
            'report_type' => $request->type,
            'generated_by' => $request->user()->id,
            'filters' => $filters,
            'status' => 'generating',
            'file_format' => $request->format,
        ]);

        $data = $this->gatherData($request->type, $from, $to, $columns);

        if ($request->format === 'csv') {
            $path = $this->writeCsv($request->type, $data);
            $report->update([
                'file_path' => $path,
                'status' => 'completed',
            ]);
        } else {
            $path = app(PdfService::class)->generateReport('generic-summary', $data, $filters);
            $report->update([
                'file_path' => $path,
                'status' => 'completed',
            ]);
        }

        AuditService::log('Reports', 'generate', null, ['report_id' => $report->id], $report->report_name);
        $this->saveFavoriteIfRequested($request);

        return redirect()->route('admin.reports.download', $report)->with('success', 'Report generated.');
    }

    public function download(Report $report)
    {
        if ($report->status !== 'completed' || ! $report->file_path) {
            abort(404);
        }
        $full = storage_path('app/public/' . $report->file_path);
        if (! is_file($full)) {
            abort(404);
        }

        return response()->download($full);
    }

    private function gatherData(string $type, string $from, string $to, array $columns = []): array
    {
        $data = match ($type) {
            'residents' => [
                'title' => 'Residents summary',
                'rows' => Resident::orderBy('full_name')->get()->map(fn ($r) => [
                    'resident_number' => $r->resident_number,
                    'full_name' => $r->full_name,
                    'gender' => $r->gender,
                    'address' => $r->address,
                    'contact' => $r->contact_number,
                ])->all(),
            ],
            default => [
                'title' => 'Requests (' . $from . ' – ' . $to . ')',
                'summary' => $this->requestSummary($from, $to),
                'rows' => DocumentRequest::with('documentType')
                    ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                    ->latest()
                    ->limit(500)
                    ->get()
                    ->map(fn ($d) => [
                        'request_number' => $d->request_number,
                        'status' => $d->status,
                        'type' => $d->documentType?->name,
                        'created_at' => $d->created_at?->toDateTimeString(),
                    ])
                    ->all(),
            ],
        };

        if ($columns && ! empty($data['rows'])) {
            $available = array_keys($data['rows'][0]);
            $selected = array_values(array_intersect($columns, $available));
            $allowed = array_flip($selected ?: $available);
            $data['rows'] = collect($data['rows'])
                ->map(fn ($row) => array_intersect_key($row, $allowed))
                ->all();
        }

        return $data;
    }

    private function requestSummary(string $from, string $to): array
    {
        $base = DocumentRequest::query()
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);

        return [
            'total' => (clone $base)->count(),
            'pending' => (clone $base)->where('status', 'pending')->count(),
            'approved' => (clone $base)->where('status', 'approved')->count(),
            'rejected' => (clone $base)->where('status', 'rejected')->count(),
        ];
    }

    private function writeCsv(string $type, array $data): string
    {
        $dir = 'reports';
        \Storage::disk('public')->makeDirectory($dir);
        $name = 'REPORT_' . $type . '_' . now()->format('YmdHis') . '.csv';
        $path = $dir . '/' . $name;
        $full = storage_path('app/public/' . $path);

        $fp = fopen($full, 'w');
        if (! empty($data['rows'])) {
            fputcsv($fp, array_keys($data['rows'][0]));
            foreach ($data['rows'] as $row) {
                fputcsv($fp, $row);
            }
        }
        fclose($fp);

        return $path;
    }

    private function saveFavoriteIfRequested(Request $request): void
    {
        if (! $request->filled('favorite_name')) {
            return;
        }

        $favorites = json_decode((string) SystemSetting::get('favorite_report_configs', '[]'), true) ?: [];
        $favorites[] = [
            'name' => $request->favorite_name,
            'type' => $request->type,
            'format' => $request->format,
            'columns' => $request->input('columns', []),
            'saved_at' => now()->toDateTimeString(),
        ];

        SystemSetting::setWithMeta('favorite_report_configs', json_encode(array_slice($favorites, -10)), 'reports', 'json', 'Saved report builder presets.');
    }
}
