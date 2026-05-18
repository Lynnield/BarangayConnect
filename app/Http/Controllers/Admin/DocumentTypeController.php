<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Concerns\SortsQueries;
use App\Models\DocumentType;
use App\Support\ListSorts;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DocumentTypeController extends Controller
{
    use SortsQueries;

    public function index(Request $request)
    {
        $query = DocumentType::query();
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")->orWhere('slug', 'like', "%{$s}%");
            });
        }
        $this->applyListSort($query, $request, ListSorts::documentTypes(), 'name', 'asc');
        $documentTypes = $query->paginate(15)->withQueryString();

        return view('admin.document-types.index', compact('documentTypes'));
    }

    public function create()
    {
        return view('admin.document-types.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        $data['is_active'] = $request->boolean('is_active', true);
        $type = DocumentType::create($data);
        AuditService::log('DocumentTypes', 'create', null, $type->toArray(), $type->name);

        return redirect()->route('admin.document-types.index')->with('success', 'Document type created.');
    }

    public function show(DocumentType $documentType)
    {
        return view('admin.document-types.show', compact('documentType'));
    }

    public function edit(DocumentType $documentType)
    {
        return view('admin.document-types.edit', compact('documentType'));
    }

    public function update(Request $request, DocumentType $documentType)
    {
        $old = $documentType->toArray();
        $data = $this->validateData($request, $documentType->id);
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        $data['is_active'] = $request->boolean('is_active', true);
        $documentType->update($data);
        AuditService::log('DocumentTypes', 'update', $old, $documentType->fresh()->toArray(), $documentType->name);

        return redirect()->route('admin.document-types.index')->with('success', 'Document type updated.');
    }

    public function destroy(DocumentType $documentType)
    {
        if ($documentType->documentRequests()->exists()) {
            return back()->withErrors(['error' => 'Cannot delete: requests exist for this type.']);
        }
        $name = $documentType->name;
        $documentType->delete();
        AuditService::log('DocumentTypes', 'delete', ['name' => $name], null, $name);

        return redirect()->route('admin.document-types.index')->with('success', 'Document type deleted.');
    }

    private function validateData(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'slug' => [
                'nullable', 'string', 'max:255',
                Rule::unique('document_types', 'slug')->ignore($id),
            ],
            'description' => 'nullable|string',
            'fee' => 'required|numeric|min:0',
            'processing_days' => 'required|integer|min:1',
            'required_fields' => 'nullable|array',
            'required_attachments' => 'nullable|array',
        ]);
    }
}
