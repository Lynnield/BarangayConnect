<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Concerns\SortsQueries;
use App\Models\{DocumentRequest, DocumentType};
use App\Support\ListSorts;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    use SortsQueries;

    public function index(Request $request)
    {
        $query = DocumentRequest::with(['resident', 'documentType']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('request_number', 'like', "%{$s}%")
                    ->orWhereHas('resident', fn ($r) => $r->where('full_name', 'like', "%{$s}%"));
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('document_type')) {
            $query->where('document_type_id', $request->document_type);
        }

        $this->applyListSort($query, $request, ListSorts::documentRequests(), 'created_at', 'desc');
        $requests = $query->paginate(25)->withQueryString();
        $documentTypes = DocumentType::orderBy('name')->get();

        return view('admin.requests.index', compact('requests', 'documentTypes'));
    }

    public function show(DocumentRequest $documentRequest)
    {
        $documentRequest->load([
            'resident.user',
            'documentType',
            'attachments',
            'processedBy',
            'approvedBy',
            'appointments',
            'statusLogs.changedBy',
        ]);

        return view('admin.requests.show', compact('documentRequest'));
    }
}
