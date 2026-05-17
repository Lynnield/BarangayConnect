<?php

namespace App\Services;

use App\Models\{Report, DocumentRequest, Resident, User};
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReportDeliveryMail;
use Carbon\Carbon;

class ReportService
{
    public function __construct(
        protected PdfService $pdfService
    ) {}

    /**
     * Generate and optionally email a report.
     */
    public function generateReport(string $type, string $format = 'pdf', ?array $filters = null, ?int $userId = null, bool $sendEmail = false): Report
    {
        $from = $filters['date_from'] ?? now()->startOfMonth()->toDateString();
        $to = $filters['date_to'] ?? now()->toDateString();

        $report = Report::create([
            'report_name' => ucfirst(str_replace('_', ' ', $type)) . ' ' . now()->format('Y-m-d H:i'),
            'report_type' => $type,
            'generated_by' => $userId,
            'filters' => $filters,
            'status' => 'generating',
            'file_format' => $format,
        ]);

        try {
            $data = $this->gatherData($type, $from, $to);
            $path = '';

            if ($format === 'csv') {
                $path = $this->writeCsv($type, $data);
            } else {
                $path = $this->pdfService->generateReport('generic-summary', $data, $filters);
            }

            $report->update([
                'file_path' => $path,
                'status' => 'completed',
            ]);

            if ($sendEmail && $userId) {
                $user = User::find($userId);
                if ($user && $user->email) {
                    Mail::to($user->email)->send(new ReportDeliveryMail($report));
                }
            }

            return $report;
        } catch (\Throwable $e) {
            $report->update(['status' => 'failed', 'notes' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Run automated scheduled reports.
     */
    public function runScheduledReports(string $frequency): void
    {
        $type = match($frequency) {
            'daily' => 'daily_summary',
            'weekly' => 'weekly_summary',
            'monthly' => 'monthly_summary',
            default => 'summary'
        };

        $from = match($frequency) {
            'daily' => now()->subDay()->toDateString(),
            'weekly' => now()->subWeek()->toDateString(),
            'monthly' => now()->subMonth()->toDateString(),
            default => now()->subDay()->toDateString()
        };

        $to = now()->toDateString();

        // Get admins to notify
        $admins = User::whereHas('role', fn($q) => $q->where('slug', 'admin'))->get();

        foreach ($admins as $admin) {
            $this->generateReport($type, 'pdf', [
                'date_from' => $from,
                'date_to' => $to,
                'automated' => true,
                'frequency' => $frequency
            ], $admin->id, true);
        }
    }

    protected function gatherData(string $type, string $from, string $to): array
    {
        // Re-use logic from Controller or expand it here
        return [
            'title' => ucfirst(str_replace('_', ' ', $type)),
            'date_range' => "$from to $to",
            'summary' => $this->requestSummary($from, $to),
            'rows' => DocumentRequest::with(['documentType', 'resident'])
                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                ->latest()
                ->limit(1000)
                ->get()
                ->map(fn($d) => [
                    'Number' => $d->request_number,
                    'Resident' => $d->resident?->full_name,
                    'Type' => $d->documentType?->name,
                    'Status' => ucfirst($d->status),
                    'Date' => $d->created_at->format('M d, Y'),
                ])->toArray()
        ];
    }

    protected function requestSummary(string $from, string $to): array
    {
        $base = DocumentRequest::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
        return [
            'Total Requests' => (clone $base)->count(),
            'Pending' => (clone $base)->where('status', 'pending')->count(),
            'Approved' => (clone $base)->where('status', 'approved')->count(),
            'Rejected' => (clone $base)->where('status', 'rejected')->count(),
        ];
    }

    protected function writeCsv(string $type, array $data): string
    {
        $dir = 'reports';
        Storage::disk('public')->makeDirectory($dir);
        $name = 'REPORT_' . $type . '_' . now()->format('YmdHis') . '.csv';
        $path = $dir . '/' . $name;
        $full = storage_path('app/public/' . $path);

        $fp = fopen($full, 'w');
        if (!empty($data['rows'])) {
            fputcsv($fp, array_keys($data['rows'][0]));
            foreach ($data['rows'] as $row) {
                fputcsv($fp, $row);
            }
        }
        fclose($fp);

        return $path;
    }
}
