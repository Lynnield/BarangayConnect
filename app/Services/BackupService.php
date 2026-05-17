<?php

namespace App\Services;

use App\Models\Backup;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\BackupNotificationMail;
use App\Models\User;

class BackupService
{
    /**
     * Run a database backup.
     */
    public function runDatabaseBackup(?int $userId = null, string $type = 'manual'): Backup
    {
        $connection = Config::get('database.default');
        $driver = Config::get("database.connections.{$connection}.driver");

        $name = 'backup_db_' . now()->format('Ymd_His');
        $backup = Backup::create([
            'backup_name' => $name,
            'backup_type' => $type,
            'status' => 'running',
            'generated_by' => $userId,
        ]);

        try {
            // Ensure backups directory exists with proper permissions
            $backupDir = storage_path('app/backups');
            if (!File::isDirectory($backupDir)) {
                File::makeDirectory($backupDir, 0755, true);
                @chmod($backupDir, 0755);
            }

            $relative = 'backups/' . $name;

            if ($driver === 'sqlite') {
                $dbPath = database_path('database.sqlite');
                if (! File::exists($dbPath)) {
                    throw new \RuntimeException('SQLite database file not found at: ' . $dbPath);
                }
                $target = storage_path('app/' . $relative . '.sqlite');
                
                // Use native copy with better error handling
                if (!copy($dbPath, $target)) {
                    throw new \RuntimeException('Failed to copy database file. Check disk space and permissions.');
                }

                // Verify file was created
                if (!File::exists($target)) {
                    throw new \RuntimeException('Database backup file was not created.');
                }
                
                @chmod($target, 0644);
                $size = filesize($target);
                if ($size === false || $size < 1) {
                    throw new \RuntimeException('Database backup file is empty or invalid.');
                }
                $path = $relative . '.sqlite';
            } elseif ($driver === 'mysql') {
                $path = $relative . '.sql';
                $full = storage_path('app/' . $path);
                $this->dumpMysql($full);
                $size = filesize($full);
                if ($size === false || $size < 1) {
                    throw new \RuntimeException('MySQL dump resulted in empty file.');
                }
            } else {
                throw new \RuntimeException('Unsupported DB driver for backup: ' . $driver);
            }

            $backup->update([
                'file_path' => $path,
                'file_size' => $size,
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            $this->notifySuccess($backup);
            return $backup;

        } catch (\Throwable $e) {
            $backup->update([
                'status' => 'failed',
                'notes' => $e->getMessage(),
            ]);

            $this->notifyFailure($backup, $e->getMessage());
            throw $e;
        }
    }

    /**
     * Run a file upload backup (zip storage/app/public).
     */
    public function runFilesBackup(?int $userId = null, string $type = 'manual'): Backup
    {
        $name = 'backup_files_' . now()->format('Ymd_His');
        $backup = Backup::create([
            'backup_name' => $name,
            'backup_type' => $type,
            'status' => 'running',
            'generated_by' => $userId,
        ]);

        try {
            // Ensure backups directory exists with proper permissions
            $backupDir = storage_path('app/backups');
            if (!File::isDirectory($backupDir)) {
                File::makeDirectory($backupDir, 0755, true);
                @chmod($backupDir, 0755);
            }

            $relative = 'backups/' . $name . '.zip';
            $zipPath = storage_path('app/' . $relative);
            $sourcePath = storage_path('app/public');

            if (! File::exists($sourcePath)) {
                File::makeDirectory($sourcePath, 0755, true);
            }

            $zip = new \ZipArchive();
            $openResult = $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
            if ($openResult !== true) {
                throw new \RuntimeException('Could not create zip file. Error code: ' . $openResult);
            }

            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($sourcePath),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            $fileCount = 0;
            foreach ($files as $name => $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($sourcePath) + 1);
                    if (!$zip->addFile($filePath, $relativePath)) {
                        \Log::warning("Failed to add file to zip: {$filePath}");
                    } else {
                        $fileCount++;
                    }
                }
            }

            $zip->close();

            // Verify zip was created and has content
            if (!File::exists($zipPath)) {
                throw new \RuntimeException('Backup zip file was not created.');
            }
            
            $size = filesize($zipPath);
            if ($size === false || $size < 100) {
                throw new \RuntimeException("Backup zip file is invalid or empty ({$size} bytes). Files archived: {$fileCount}");
            }

            @chmod($zipPath, 0644);

            $backup->update([
                'file_path' => $relative,
                'file_size' => $size,
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            $this->notifySuccess($backup);
            return $backup;

        } catch (\Throwable $e) {
            $backup->update([
                'status' => 'failed',
                'notes' => $e->getMessage(),
            ]);

            $this->notifyFailure($backup, $e->getMessage());
            throw $e;
        }
    }

    /**
     * Clean up old backups based on retention policy (default 30 days).
     */
    public function cleanOldBackups(int $days = 30): int
    {
        $backups = Backup::where('created_at', '<', now()->subDays($days))->get();
        $count = 0;

        foreach ($backups as $backup) {
            if ($backup->file_path) {
                $full = storage_path('app/' . $backup->file_path);
                if (is_file($full)) {
                    File::delete($full);
                }
            }
            $backup->delete();
            $count++;
        }

        return $count;
    }

    protected function dumpMysql(string $targetPath): void
    {
        $database = Config::get('database.connections.mysql.database');
        $username = Config::get('database.connections.mysql.username');
        $password = Config::get('database.connections.mysql.password');
        $host = Config::get('database.connections.mysql.host', '127.0.0.1');
        $port = Config::get('database.connections.mysql.port', 3306);

        // Check if mysqldump is available
        $mysqldump = 'mysqldump';
        exec('where ' . $mysqldump . ' 2>/dev/null', $output, $code);
        if ($code !== 0 && PHP_OS_FAMILY !== 'Windows') {
            // Try common install paths on Unix
            foreach (['/usr/bin/mysqldump', '/usr/local/bin/mysqldump'] as $path) {
                if (file_exists($path)) {
                    $mysqldump = $path;
                    break;
                }
            }
        }

        // Build mysqldump command
        $cmd = sprintf(
            '%s --user=%s --password=%s --host=%s --port=%d %s > %s 2>&1',
            escapeshellcmd($mysqldump),
            escapeshellarg($username),
            escapeshellarg((string) $password),
            escapeshellarg($host),
            (int)$port,
            escapeshellarg($database),
            escapeshellarg($targetPath)
        );

        $code = 0;
        $output = '';
        exec($cmd, $output, $code);
        
        if ($code !== 0 || ! is_file($targetPath) || filesize($targetPath) < 100) {
            $errorMsg = implode("\n", $output);
            \Log::error('MySQL dump failed. Command: ' . $cmd . "\nOutput: " . $errorMsg);
            throw new \RuntimeException('MySQL dump failed. Ensure MySQL client tools are installed and database credentials are correct. Error: ' . substr($errorMsg, 0, 200));
        }
    }

    protected function notifySuccess(Backup $backup): void
    {
        $adminEmail = Config::get('mail.from.address');
        if ($adminEmail) {
            try {
                Mail::to($adminEmail)->send(new BackupNotificationMail($backup, true));
            } catch (\Throwable $e) {
                \Log::error('Failed to send backup success notification: ' . $e->getMessage());
            }
        }
    }

    protected function notifyFailure(Backup $backup, string $error): void
    {
        $adminEmail = Config::get('mail.from.address');
        if ($adminEmail) {
            try {
                Mail::to($adminEmail)->send(new BackupNotificationMail($backup, false, $error));
            } catch (\Throwable $e) {
                \Log::error('Failed to send backup failure notification: ' . $e->getMessage());
            }
        }

        $sms = app(SmsService::class);
        User::whereHas('role', fn ($q) => $q->where('slug', 'admin'))->get()
            ->each(fn (User $admin) => $sms->send($admin->phone, config('app.name') . ' backup failed: ' . $error));
    }
}
