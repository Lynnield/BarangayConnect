<?php

namespace App\Services;

use App\Models\Resident;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResidentExportService
{
    public function __construct(private SimpleXlsxService $xlsx)
    {
    }

    /**
     * Export residents to CSV.
     */
    public function exportCsv(): StreamedResponse
    {
        $filename = 'residents_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                'resident_number', 'full_name', 'gender', 'birthdate', 
                'civil_status', 'address', 'contact_number', 'email', 'occupation'
            ]);

            Resident::orderBy('id')->chunk(500, function ($chunk) use ($out) {
                foreach ($chunk as $r) {
                    fputcsv($out, [
                        $r->resident_number,
                        $r->full_name,
                        $r->gender,
                        $r->birthdate?->format('Y-m-d'),
                        $r->civil_status,
                        $r->address,
                        $r->contact_number,
                        $r->email,
                        $r->occupation,
                    ]);
                }
            });
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Export residents to JSON.
     */
    public function exportJson(): StreamedResponse
    {
        $filename = 'residents_' . now()->format('Ymd_His') . '.json';

        return response()->streamDownload(function () {
            echo '[';
            $first = true;
            
            Resident::orderBy('id')->chunk(500, function ($chunk) use (&$first) {
                foreach ($chunk as $r) {
                    if (!$first) echo ',';
                    echo json_encode($r->makeHidden(['id', 'created_at', 'updated_at', 'deleted_at']));
                    $first = false;
                }
            });
            
            echo ']';
        }, $filename, [
            'Content-Type' => 'application/json',
        ]);
    }

    public function exportXlsx()
    {
        $filename = 'residents_' . now()->format('Ymd_His') . '.xlsx';
        $path = tempnam(sys_get_temp_dir(), 'residents_export_') . '.xlsx';
        $rows = [[
            'resident_number', 'full_name', 'gender', 'birthdate',
            'civil_status', 'address', 'contact_number', 'email', 'occupation',
        ]];

        Resident::orderBy('id')->chunk(500, function ($chunk) use (&$rows) {
            foreach ($chunk as $r) {
                $rows[] = [
                    $r->resident_number,
                    $r->full_name,
                    $r->gender,
                    $r->birthdate?->format('Y-m-d'),
                    $r->civil_status,
                    $r->address,
                    $r->contact_number,
                    $r->email,
                    $r->occupation,
                ];
            }
        });

        $this->xlsx->write($path, $rows);

        return response()->download(
            $path,
            $filename,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        )->deleteFileAfterSend(true);
    }
}
