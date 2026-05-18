<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Concerns\SortsQueries;
use App\Models\AuditLog;
use App\Support\ListSorts;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditLogController extends Controller
{
    use SortsQueries;

    public function index(Request $request)
    {
        $query = AuditLog::with('user');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('module', 'like', "%{$s}%")
                    ->orWhere('action', 'like', "%{$s}%")
                    ->orWhere('description', 'like', "%{$s}%");
            });
        }
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $this->applyListSort($query, $request, ListSorts::auditLogs(), 'created_at', 'desc');
        $logs = $query->paginate(40)->withQueryString();
        $modules = AuditLog::query()->select('module')->distinct()->pluck('module');

        return view('admin.audit-logs.index', compact('logs', 'modules'));
    }

    public function export(Request $request): StreamedResponse
    {
        $query = AuditLog::orderByDesc('created_at');
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $filename = 'audit_logs_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['id', 'user', 'module', 'action', 'description', 'ip', 'created_at']);

            $query->chunk(500, function ($chunk) use ($out) {
                foreach ($chunk as $log) {
                    fputcsv($out, [
                        $log->id,
                        $log->user_name,
                        $log->module,
                        $log->action,
                        $log->description,
                        $log->ip_address,
                        $log->created_at?->toDateTimeString(),
                    ]);
                }
            });
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
