<?php

namespace App\Services;

use App\Models\{DocumentRequest, SystemSetting};
use Barryvdh\DomPDF\Facade\Pdf;

class PdfService
{
    public function generateRequestDocument(DocumentRequest $documentRequest): string
    {
        $documentRequest->load(['resident', 'documentType', 'approvedBy']);

        $settings = [
            'barangay_name' => SystemSetting::get('barangay_name', 'Barangay San Jose'),
            'city' => SystemSetting::get('city', 'Cagayan de Oro City'),
            'captain_name' => SystemSetting::get('captain_name', 'HON. JUAN DELA CRUZ'),
            'secretary_name' => SystemSetting::get('secretary_name', 'MARIA SANTOS'),
        ];

        $viewName = match($documentRequest->documentType->slug) {
            'barangay-clearance' => 'pdfs.barangay-clearance',
            'certificate-of-residency' => 'pdfs.certificate-of-residency',
            'certificate-of-indigency' => 'pdfs.certificate-of-indigency',
            'business-permit' => 'pdfs.business-permit',
            default => 'pdfs.general-certificate',
        };

        $pdf = Pdf::loadView($viewName, [
            'request' => $documentRequest,
            'settings' => $settings,
            'generated_at' => now(),
        ])->setPaper('a4')->setOptions(['isHtml5ParserEnabled' => true]);

        $filename = "DOC_{$documentRequest->request_number}_" . now()->format('YmdHis') . '.pdf';
        $path = "pdfs/{$documentRequest->id}/$filename";

        \Storage::disk('public')->makeDirectory("pdfs/{$documentRequest->id}");
        \Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    public function generateReport(string $type, array $data, array $filters = []): string
    {
        $settings = [
            'barangay_name' => SystemSetting::get('barangay_name', 'Barangay San Jose'),
            'city' => SystemSetting::get('city', 'Cagayan de Oro City'),
        ];

        $pdf = Pdf::loadView("pdfs.reports.{$type}", [
            'data' => $data,
            'settings' => $settings,
            'filters' => $filters,
            'generated_at' => now(),
        ])->setPaper('a4', 'landscape');

        $filename = "REPORT_{$type}_" . now()->format('YmdHis') . '.pdf';
        $path = "reports/$filename";

        \Storage::disk('public')->makeDirectory('reports');
        \Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    public function generateAcknowledgementSlip(DocumentRequest $documentRequest): string
    {
        $settings = [
            'barangay_name' => SystemSetting::get('barangay_name', 'Barangay San Jose'),
            'city' => SystemSetting::get('city', 'Cagayan de Oro City'),
        ];

        $pdf = Pdf::loadView('pdfs.acknowledgement-slip', [
            'request' => $documentRequest,
            'settings' => $settings,
            'generated_at' => now(),
        ])->setPaper('a4');

        $filename = "SLIP_{$documentRequest->request_number}.pdf";
        $path = "slips/{$filename}";

        \Storage::disk('public')->makeDirectory('slips');
        \Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }
}
