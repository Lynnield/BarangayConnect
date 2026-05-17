<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Backup;
use App\Services\AuditService;
use App\Services\BackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class BackupController extends Controller
{
    public function index()
    {
        $backups = Backup::with('generatedBy')->latest()->paginate(20);

        return view('admin.backups.index', compact('backups'));
    }

    public function create(Request $request, BackupService $backupService)
    {
        try {
            $type = $request->get('type', 'db');
            
            if ($type === 'files') {
                $backup = $backupService->runFilesBackup($request->user()->id, 'manual');
            } else {
                $backup = $backupService->runDatabaseBackup($request->user()->id, 'manual');
            }

            AuditService::log('Backups', 'create', null, ['backup_id' => $backup->id], $backup->backup_name);
            
            return back()->with('success', 'Backup completed successfully.');
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Backup failed: ' . $e->getMessage()]);
        }
    }

    public function download(Backup $backup)
    {
        if ($backup->status !== 'completed' || ! $backup->file_path) {
            abort(404);
        }
        $full = storage_path('app/' . $backup->file_path);
        if (! is_file($full)) {
            abort(404);
        }

        return response()->download($full);
    }

    public function destroy(Backup $backup)
    {
        if ($backup->file_path) {
            $full = storage_path('app/' . $backup->file_path);
            if (is_file($full)) {
                File::delete($full);
            }
        }
        $backup->delete();
        AuditService::log('Backups', 'delete', null, null, 'Backup record removed');

        return back()->with('success', 'Backup entry removed.');
    }

    private function dumpMysql(string $targetPath): void
    {
        $database = Config::get('database.connections.mysql.database');
        $username = Config::get('database.connections.mysql.username');
        $password = Config::get('database.connections.mysql.password');
        $host = Config::get('database.connections.mysql.host', '127.0.0.1');

        $mysqldump = 'mysqldump';
        $cmd = sprintf(
            '%s --user=%s --password=%s --host=%s %s > %s',
            escapeshellcmd($mysqldump),
            escapeshellarg($username),
            escapeshellarg((string) $password),
            escapeshellarg($host),
            escapeshellarg($database),
            escapeshellarg($targetPath)
        );

        $code = 0;
        passthru($cmd, $code);
        if ($code !== 0 || ! is_file($targetPath) || filesize($targetPath) < 10) {
            throw new \RuntimeException('mysqldump failed or is not available on PATH. Install MySQL client tools or use SQLite for local backups.');
        }
    }
}
