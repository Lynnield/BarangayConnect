<?php

namespace App\Services;

use App\Models\Resident;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\UploadedFile;

class ResidentImportService
{
    public function __construct(private SimpleXlsxService $xlsx)
    {
    }

    public function importFile(UploadedFile $file, bool $previewOnly = false): array
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if ($extension === 'xlsx') {
            return $this->importRows($this->xlsx->read($file->getRealPath()), $previewOnly);
        }

        $handle = fopen($file->getRealPath(), 'r');
        $rows = [];
        while (($row = fgetcsv($handle)) !== false) {
            $rows[] = $row;
        }
        fclose($handle);

        return $this->importRows($rows, $previewOnly);
    }

    /**
     * Import residents from a CSV file handle.
     */
    public function importCsv($handle, bool $previewOnly = false): array
    {
        $rows = [];
        while (($row = fgetcsv($handle)) !== false) {
            $rows[] = $row;
        }

        return $this->importRows($rows, $previewOnly);
    }

    public function importRows(array $rows, bool $previewOnly = false): array
    {
        $header = array_shift($rows);
        if (!$header) {
            return ['success' => false, 'message' => 'Empty file.'];
        }

        $header = array_map(fn($h) => strtolower(trim((string)$h)), $header);
        
        $imported = 0;
        $failed = 0;
        $errors = [];
        $rowNumber = 1; // Header is row 1

        $preview = [];

        foreach ($rows as $row) {
            $rowNumber++;
            $data = @array_combine($header, $row);
            
            if (!$data) {
                $failed++;
                $errors[] = [
                    'row' => $rowNumber,
                    'name' => 'Unknown',
                    'errors' => ['Column mismatch or invalid data format.']
                ];
                continue;
            }

            $data = $this->normalizeRow($data);

            $validator = Validator::make($data, [
                'full_name' => 'required|string|max:255',
                'gender' => 'required|in:male,female,other',
                'birthdate' => 'required|date',
                'civil_status' => 'required|in:single,married,widowed,separated,divorced',
                'address' => 'required|string',
                'contact_number' => 'nullable|string|max:50',
                'email' => 'nullable|email',
                'occupation' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                $failed++;
                $errors[] = [
                    'row' => $rowNumber,
                    'name' => $data['full_name'] ?? 'Unknown',
                    'errors' => $validator->errors()->all()
                ];
                continue;
            }

            $validated = $validator->validated();

            try {
                if (! $previewOnly) {
                    Resident::create($validated);
                }

                $imported++;
                if (count($preview) < 10) {
                    $preview[] = $validated + ['row' => $rowNumber, 'status' => 'valid'];
                }
            } catch (\Throwable $e) {
                $failed++;
                $errors[] = [
                    'row' => $rowNumber,
                    'name' => $data['full_name'] ?? 'Unknown',
                    'errors' => [$e->getMessage()]
                ];
            }
        }

        return [
            'success' => true,
            'imported_count' => $imported,
            'failed_count' => $failed,
            'errors' => $errors,
            'preview' => $preview,
            'preview_only' => $previewOnly,
        ];
    }

    /**
     * Generate a CSV template for import.
     */
    public function getTemplateCsv(): string
    {
        $headers = [
            'full_name', 'gender', 'birthdate', 'civil_status', 
            'address', 'contact_number', 'email', 'occupation'
        ];
        
        $example = [
            'Juan Dela Cruz', 'male', '1990-01-01', 'single',
            '123 Street, Brgy. San Jose', '09123456789', 'juan@example.com', 'Teacher'
        ];

        $out = fopen('php://temp', 'r+');
        fputcsv($out, $headers);
        fputcsv($out, $example);
        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);

        return $csv;
    }

    public function getTemplateXlsx(): string
    {
        $path = tempnam(sys_get_temp_dir(), 'resident_template_') . '.xlsx';
        $this->xlsx->write($path, [
            [
                'full_name', 'gender', 'birthdate', 'civil_status',
                'address', 'contact_number', 'email', 'occupation',
            ],
            [
                'Juan Dela Cruz', 'male', '1990-01-01', 'single',
                '123 Street, Brgy. San Jose', '09123456789', 'juan@example.com', 'Teacher',
            ],
        ]);

        return $path;
    }

    private function normalizeRow(array $data): array
    {
        if (isset($data['birthdate']) && is_numeric($data['birthdate'])) {
            $days = (int) $data['birthdate'];
            $data['birthdate'] = now()
                ->setTimezone('UTC')
                ->setDate(1899, 12, 30)
                ->startOfDay()
                ->addDays($days)
                ->toDateString();
        }

        return $data;
    }
}
