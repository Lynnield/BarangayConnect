<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\{DocumentRequest, DocumentType, Resident, RequestStatusLog, ActivityFeed};
use App\Notifications\RequestStatusNotification;
use App\Services\{AuditService, PdfService};
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function index(Request $request)
    {
        $query = DocumentRequest::with(['resident', 'documentType', 'processedBy']);

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('request_number', 'like', "%{$request->search}%")
                  ->orWhereHas('resident', fn($r) => $r->where('full_name', 'like', "%{$request->search}%"));
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->document_type) {
            $query->where('document_type_id', $request->document_type);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $requests = $query->latest()->paginate(20)->withQueryString();
        $documentTypes = DocumentType::where('is_active', true)->get();
        $statusCounts = DocumentRequest::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')->pluck('count', 'status');

        return view('staff.requests.index', compact('requests', 'documentTypes', 'statusCounts'));
    }

    public function show(DocumentRequest $documentRequest)
    {
        $documentRequest->load([
            'resident', 'documentType', 'attachments', 'processedBy',
            'approvedBy', 'appointments', 'statusLogs.changedBy'
        ]);
        return view('staff.requests.show', compact('documentRequest'));
    }

    public function updateStatus(Request $request, DocumentRequest $documentRequest)
    {
        $request->validate([
            'status' => 'required|in:under_review,for_revision,approved,rejected,ready_for_pickup,released',
            'remarks' => 'nullable|string|max:1000',
            'rejection_reason' => 'required_if:status,rejected|nullable|string|max:1000',
            'revision_notes' => 'required_if:status,for_revision|nullable|string|max:1000',
        ]);

        $oldStatus = $documentRequest->status;
        $newStatus = $request->status;

        $updateData = [
            'status' => $newStatus,
            'remarks' => $request->remarks,
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ];

        if ($newStatus === 'rejected') {
            $updateData['rejection_reason'] = $request->rejection_reason;
        }

        if ($newStatus === 'for_revision') {
            $updateData['revision_notes'] = $request->revision_notes;
        }

        if ($newStatus === 'approved') {
            $updateData['approved_by'] = auth()->id();
            $updateData['approved_at'] = now();
        }

        if ($newStatus === 'released') {
            $updateData['released_at'] = now();
        }

        $documentRequest->update($updateData);

        // Log status change
        RequestStatusLog::create([
            'document_request_id' => $documentRequest->id,
            'from_status' => $oldStatus,
            'to_status' => $newStatus,
            'notes' => $request->remarks,
            'changed_by' => auth()->id(),
        ]);

        // Notify resident
        if ($documentRequest->resident?->user) {
            try {
                $documentRequest->resident->user->notify(
                    new RequestStatusNotification($documentRequest, $oldStatus, $newStatus)
                );
            } catch (\Exception $e) {
                \Log::error('Notification failed: ' . $e->getMessage());
            }
        }

        ActivityFeed::log(
            'request_status',
            "Request #{$documentRequest->request_number} status changed from {$oldStatus} to {$newStatus}"
        );

        AuditService::log(
            'Requests', 'status_update',
            ['status' => $oldStatus],
            ['status' => $newStatus],
            "Request #{$documentRequest->request_number} status updated to {$newStatus}",
            DocumentRequest::class,
            $documentRequest->id
        );

        return back()->with('success', 'Request status updated successfully.');
    }

    public function generatePdf(DocumentRequest $documentRequest)
    {
        try {
            $pdfPath = app(PdfService::class)->generateRequestDocument($documentRequest);
            $documentRequest->update(['pdf_path' => $pdfPath]);

            AuditService::log('Requests', 'generate_pdf', null, null,
                "PDF generated for request #{$documentRequest->request_number}");

            return response()->download(storage_path('app/public/' . $pdfPath));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to generate PDF: ' . $e->getMessage()]);
        }
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject,mark_ready',
            'request_ids' => 'required|array',
            'request_ids.*' => 'exists:document_requests,id',
        ]);

        $requests = DocumentRequest::whereIn('id', $request->request_ids)->get();
        $count = 0;

        foreach ($requests as $req) {
            $status = match ($request->action) {
                'approve' => 'approved',
                'reject' => 'rejected',
                'mark_ready' => 'ready_for_pickup',
            };

            $allowed = match ($request->action) {
                'approve' => $req->canBeApproved(),
                'reject' => $req->canBeRejected(),
                'mark_ready' => in_array($req->status, ['approved', 'under_review', 'pending']),
                default => false,
            };

            if (! $allowed) {
                continue;
            }

            $oldStatus = $req->status;
            $req->update(['status' => $status, 'processed_by' => auth()->id()]);
            RequestStatusLog::create([
                'document_request_id' => $req->id,
                'from_status' => $oldStatus,
                'to_status' => $status,
                'changed_by' => auth()->id(),
                'notes' => 'Bulk action: ' . $request->action,
            ]);
            $count++;
        }

        AuditService::log('Requests', 'bulk_action', null, ['action' => $request->action, 'count' => $count]);

        return back()->with('success', "{$count} request(s) updated successfully.");
    }
}
