<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\{DocumentRequest, DocumentType, Resident, RequestAttachment, ActivityFeed};
use App\Notifications\RequestSubmittedNotification;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DocumentRequestController extends Controller
{
    public function index()
    {
        $resident = auth()->user()->resident;

        if (!$resident) {
            return redirect()->route('resident.profile.edit')
                ->with('warning', 'Please complete your resident profile first.');
        }

        $requests = $resident->documentRequests()
            ->with('documentType')
            ->latest()
            ->paginate(10);

        return view('resident.requests.index', compact('requests'));
    }

    public function create()
    {
        $resident = auth()->user()->resident;

        if (!$resident) {
            return redirect()->route('resident.profile.edit')
                ->with('warning', 'Please complete your resident profile before requesting documents.');
        }

        if (!$resident->isVerified) {
            return redirect()->route('resident.requests.index')
                ->with('warning', 'Only verified residents may request documents. Please wait for verification or contact the barangay office.');
        }

        $documentTypes = DocumentType::where('is_active', true)->get();
        return view('resident.requests.create', compact('documentTypes', 'resident'));
    }

    public function store(Request $request)
    {
        $resident = auth()->user()->resident;

        if (!$resident) {
            return back()->withErrors(['error' => 'Please complete your resident profile first.']);
        }

        if (!$resident->isVerified) {
            return back()->with('warning', 'Only verified residents may submit document requests. Please wait for verification or contact the barangay office.');
        }

        $request->validate([
            'document_type_id' => 'required|exists:document_types,id',
            'purpose' => 'required|string|max:1000',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ]);

        $documentType = DocumentType::findOrFail($request->document_type_id);

        // Check for duplicate pending requests
        $existing = $resident->documentRequests()
            ->where('document_type_id', $documentType->id)
            ->whereIn('status', ['pending', 'under_review', 'for_revision'])
            ->first();

        if ($existing) {
            return back()->withErrors([
                'document_type_id' => "You already have a pending request for {$documentType->name} (#{$existing->request_number})."
            ]);
        }

        DB::beginTransaction();
        try {
            $docRequest = DocumentRequest::create([
                'resident_id' => $resident->id,
                'document_type_id' => $documentType->id,
                'purpose' => $request->purpose,
                'status' => 'pending',
                'fee_amount' => $documentType->fee,
                'form_data' => $request->except(['_token', 'document_type_id', 'purpose', 'attachments']),
            ]);

            // Handle attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('attachments/' . $docRequest->id, 'public');
                    RequestAttachment::create([
                        'document_request_id' => $docRequest->id,
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                        'file_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'uploaded_by' => auth()->id(),
                    ]);
                }
            }

            DB::commit();

            // Notify staff
            ActivityFeed::log('request_submitted', "New request #{$docRequest->request_number} for {$documentType->name}");

            AuditService::log(
                'Requests', 'create',
                null, $docRequest->toArray(),
                "New document request: #{$docRequest->request_number}",
                DocumentRequest::class, $docRequest->id
            );

            return redirect()->route('resident.requests.show', $docRequest)
                ->with('success', "Request #{$docRequest->request_number} submitted successfully! You will be notified of any updates.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to submit request. Please try again.']);
        }
    }

    public function show(DocumentRequest $documentRequest)
    {
        // Ensure resident owns this request
        $resident = auth()->user()->resident;
        abort_if($documentRequest->resident_id !== $resident?->id, 403);

        $documentRequest->load(['documentType', 'attachments', 'appointments', 'statusLogs.changedBy']);

        return view('resident.requests.show', compact('documentRequest'));
    }

    public function cancel(DocumentRequest $documentRequest)
    {
        $resident = auth()->user()->resident;
        abort_if($documentRequest->resident_id !== $resident?->id, 403);
        abort_if(!$documentRequest->canBeCancelled(), 403, 'This request cannot be cancelled.');

        $documentRequest->update(['status' => 'cancelled']);

        AuditService::log('Requests', 'cancel', null, null,
            "Request #{$documentRequest->request_number} cancelled by resident");

        return back()->with('success', 'Request cancelled successfully.');
    }

    public function download(DocumentRequest $documentRequest)
    {
        $resident = auth()->user()->resident;
        abort_if($documentRequest->resident_id !== $resident?->id, 403);
        abort_if($documentRequest->status !== 'released' || !$documentRequest->pdf_path, 403);

        return response()->download(storage_path('app/public/' . $documentRequest->pdf_path));
    }
}
