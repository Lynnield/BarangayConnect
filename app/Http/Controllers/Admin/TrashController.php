<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Concerns\SortsQueries;
use App\Models\Appointment;
use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Models\Resident;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;

class TrashController extends Controller
{
    use SortsQueries;

    private array $models = [
        'users' => User::class,
        'residents' => Resident::class,
        'document_requests' => DocumentRequest::class,
        'document_types' => DocumentType::class,
        'appointments' => Appointment::class,
    ];

    public function index(Request $request)
    {
        $trash = collect($this->models)->mapWithKeys(function (string $class, string $key) use ($request) {
            $query = $class::onlyTrashed();
            $this->applyListSort($query, $request, $this->trashSortOptions($key), 'deleted_at', 'desc');

            return [$key => $query->limit(50)->get()];
        });

        return view('admin.trash.index', compact('trash'));
    }

    /** @return array<string, string> */
    private function trashSortOptions(string $type): array
    {
        return match ($type) {
            'users' => ['deleted_at' => 'deleted_at', 'name' => 'name'],
            'residents' => ['deleted_at' => 'deleted_at', 'name' => 'full_name'],
            'document_requests' => ['deleted_at' => 'deleted_at', 'name' => 'request_number'],
            'document_types' => ['deleted_at' => 'deleted_at', 'name' => 'name'],
            'appointments' => ['deleted_at' => 'deleted_at', 'name' => 'appointment_number'],
            default => ['deleted_at' => 'deleted_at'],
        };
    }

    public function restore(Request $request, string $type, int $id)
    {
        $model = $this->findTrashed($type, $id);
        $model->restore();

        AuditService::log('Trash', 'restore', null, ['type' => $type, 'id' => $id], "Restored {$type} #{$id}");

        return back()->with('success', 'Record restored.');
    }

    public function forceDelete(Request $request, string $type, int $id)
    {
        $model = $this->findTrashed($type, $id);
        $old = $model->toArray();
        $model->forceDelete();

        AuditService::log('Trash', 'force_delete', $old, null, "Permanently deleted {$type} #{$id}");

        return back()->with('success', 'Record permanently deleted.');
    }

    private function findTrashed(string $type, int $id)
    {
        abort_unless(isset($this->models[$type]), 404);

        return $this->models[$type]::onlyTrashed()->findOrFail($id);
    }
}
