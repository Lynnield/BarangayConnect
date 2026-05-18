<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Concerns\SortsQueries;
use App\Models\Resident;
use App\Support\ListSorts;
use App\Services\AuditService;
use App\Services\ResidentExportService;
use App\Services\ResidentImportService;
use Illuminate\Http\Request;

class ResidentController extends Controller
{
    use SortsQueries;

    public function index(Request $request)
    {
        $query = Resident::with('user');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('full_name', 'like', "%{$s}%")
                    ->orWhere('first_name', 'like', "%{$s}%")
                    ->orWhere('middle_name', 'like', "%{$s}%")
                    ->orWhere('last_name', 'like', "%{$s}%")
                    ->orWhere('resident_number', 'like', "%{$s}%")
                    ->orWhere('contact_number', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%");
            });
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        $this->applyListSort($query, $request, ListSorts::residents(), 'full_name', 'asc');
        $residents = $query->paginate(20)->withQueryString();

        return view('admin.residents.index', compact('residents'));
    }

    public function create()
    {
        return view('admin.residents.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $resident = Resident::create($data);
        AuditService::log('Residents', 'create', null, $resident->toArray(), "Created resident: {$resident->full_name}");

        return redirect()->route('admin.residents.index')->with('success', 'Resident saved.');
    }

    public function show(Resident $resident)
    {
        $resident->load(['user', 'documentRequests.documentType', 'appointments']);

        return view('admin.residents.show', compact('resident'));
    }

    public function edit(Resident $resident)
    {
        return view('admin.residents.edit', compact('resident'));
    }

    public function update(Request $request, Resident $resident)
    {
        $old = $resident->toArray();
        $resident->update($this->validated($request, $resident->id));
        AuditService::log('Residents', 'update', $old, $resident->fresh()->toArray(), "Updated resident: {$resident->full_name}");

        return redirect()->route('admin.residents.index')->with('success', 'Resident updated.');
    }

    public function destroy(Resident $resident)
    {
        AuditService::log('Residents', 'delete', $resident->toArray(), null, "Deleted resident: {$resident->full_name}");
        $resident->delete();

        return redirect()->route('admin.residents.index')->with('success', 'Resident deleted.');
    }

    public function importForm()
    {
        return view('admin.residents.import');
    }

    public function downloadTemplate(ResidentImportService $importService)
    {
        if (request('format') === 'xlsx') {
            return response()->download(
                $importService->getTemplateXlsx(),
                'resident_import_template.xlsx',
                ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
            )->deleteFileAfterSend(true);
        }

        return response()->streamDownload(function () use ($importService) {
            echo $importService->getTemplateCsv();
        }, 'resident_import_template.csv', ['Content-Type' => 'text/csv']);
    }

    public function previewImport(Request $request, ResidentImportService $importService)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx|max:5120',
        ]);

        $result = $importService->importFile($request->file('file'), true);

        return back()
            ->with('import_preview', $result)
            ->withInput();
    }

    public function import(Request $request, ResidentImportService $importService)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx|max:5120',
        ]);

        $result = $importService->importFile($request->file('file'));

        if (!$result['success']) {
            return back()->withErrors(['error' => $result['message']]);
        }

        AuditService::log('Residents', 'import', null, [
            'imported' => $result['imported_count'],
            'failed' => $result['failed_count']
        ], strtoupper($request->file('file')->getClientOriginalExtension()) . ' import');

        $message = "Imported {$result['imported_count']} resident(s).";
        if ($result['failed_count'] > 0) {
            $message .= " Failed: {$result['failed_count']}. Check the summary below.";
        }

        return redirect()->route('admin.residents.index')
            ->with('success', $message)
            ->with('import_errors', $result['errors']);
    }

    public function export(Request $request, ResidentExportService $exportService)
    {
        $format = $request->get('format', 'csv');

        if ($format === 'json') {
            return $exportService->exportJson();
        }

        if ($format === 'xlsx') {
            return $exportService->exportXlsx();
        }

        return $exportService->exportCsv();
    }

    private function validated(Request $request, ?int $id = null): array
    {
        $data = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'first_name' => 'required_without:full_name|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required_without:full_name|string|max:100',
            'suffix' => 'nullable|string|max:30',
            'full_name' => 'nullable|string|max:255',
            'gender' => 'required|in:male,female,other',
            'birthdate' => 'required|date',
            'civil_status' => 'required|in:single,married,widowed,separated,divorced',
            'house_number' => 'nullable|string|max:100',
            'street' => 'nullable|string|max:150',
            'purok' => 'nullable|string|max:100',
            'barangay' => 'nullable|string|max:150',
            'city' => 'nullable|string|max:150',
            'province' => 'nullable|string|max:150',
            'postal_code' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'contact_number' => 'nullable|string|max:50',
            'email' => 'nullable|email',
            'occupation' => 'nullable|string|max:255',
            'valid_id_type' => 'nullable|string|max:100',
            'valid_id_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        if (! filled($data['first_name'] ?? null) && filled($data['full_name'] ?? null)) {
            $parts = preg_split('/\s+/', trim($data['full_name'])) ?: [];
            $data['first_name'] = array_shift($parts);
            $data['last_name'] = count($parts) ? array_pop($parts) : null;
            $data['middle_name'] = implode(' ', $parts) ?: null;
        }

        $data['full_name'] = $this->composeFullName($data);
        $data['address'] = $data['address'] ?: $this->composeAddress($data);
        $data['verification_status'] = $data['verification_status'] ?? 'pending';

        return $data;
    }

    private function composeFullName(array $data): string
    {
        return implode(' ', array_filter([
            $data['first_name'] ?? null,
            $data['middle_name'] ?? null,
            $data['last_name'] ?? null,
            $data['suffix'] ?? null,
        ]));
    }

    private function composeAddress(array $data): string
    {
        return implode(', ', array_filter([
            $data['house_number'] ?? null,
            $data['street'] ?? null,
            $data['purok'] ?? null,
            $data['barangay'] ?? null,
            $data['city'] ?? null,
            $data['province'] ?? null,
            $data['postal_code'] ?? null,
        ]));
    }
}
