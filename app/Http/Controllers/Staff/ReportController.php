<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\{DocumentRequest, Report};
use App\Services\AuditService;
use App\Services\PdfService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $recent = Report::with('generatedByUser')->where('generated_by', auth()->id())->latest()->limit(15)->get();

        return view('staff.reports.index', compact('recent'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'type' => 'required|in:requests_summary,monthly',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
        ]);

        $from = $request->date_from ?? now()->startOfMonth()->toDateString();
        $to = $request->date_to ?? now()->toDateString();

        $data = [
            'title' => 'Staff report ' . $from . ' – ' . $to,
            'summary' => [
                'total' => DocumentRequest::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])->count(),
            ],
            'rows' => DocumentRequest::with('documentType')
                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                ->latest()->limit(200)->get()
                ->map(fn ($d) => [
                    'request_number' => $d->request_number,
                    'status' => $d->status,
                    'type' => $d->documentType?->name,
                ])->all(),
        ];

        $path = app(PdfService::class)->generateReport('generic-summary', $data, compact('from', 'to'));

        $report = Report::create([
            'report_name' => 'Staff ' . ucfirst($request->type) . ' ' . now()->format('Y-m-d H:i'),
            'report_type' => $request->type,
            'generated_by' => $request->user()->id,
            'file_path' => $path,
            'file_format' => 'pdf',
            'filters' => compact('from', 'to'),
            'status' => 'completed',
        ]);

        AuditService::log('Reports', 'staff_generate', null, ['report_id' => $report->id], $report->report_name);

        return response()->download(storage_path('app/public/' . $path));
    }
}
