<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Storage;

class AuditService
{
    public static function log(
        string $module,
        string $action,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $description = null,
        ?string $modelType = null,
        ?int $modelId = null
    ): void {
        AuditLog::log($module, $action, $oldValues, $newValues, $description, $modelType, $modelId);
    }

    public function archiveOlderThan(int $days = 90): int
    {
        $cutoff = now()->subDays($days);
        $logs = AuditLog::where('created_at', '<', $cutoff)->orderBy('id')->get();

        if ($logs->isEmpty()) {
            return 0;
        }

        Storage::disk('local')->makeDirectory('audit-archives');

        $path = 'audit-archives/audit_' . $cutoff->format('Ymd') . '_' . now()->format('His') . '.json';
        Storage::disk('local')->put($path, $logs->toJson(JSON_PRETTY_PRINT));

        AuditLog::whereIn('id', $logs->pluck('id'))->delete();

        return $logs->count();
    }
}
