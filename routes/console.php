<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\BackupService;
use App\Services\AuditService;
use App\Services\ReportService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Automated Backups
Schedule::call(function (BackupService $backupService) {
    $backupService->runDatabaseBackup(null, 'scheduled');
})->dailyAt(SystemSetting::get('backup_database_time', '01:00'))
    ->when(fn () => SystemSetting::bool('backup_database_enabled', true))
    ->name('database-backup')
    ->withoutOverlapping();

Schedule::call(function (BackupService $backupService) {
    $backupService->runFilesBackup(null, 'scheduled');
})->weeklyOn(SystemSetting::int('backup_files_weekday', 0), SystemSetting::get('backup_files_time', '02:00'))
    ->when(fn () => SystemSetting::bool('backup_files_enabled', true))
    ->name('files-backup')
    ->withoutOverlapping();

Schedule::call(function (BackupService $backupService) {
    $backupService->cleanOldBackups(SystemSetting::int('backup_retention_days', 30));
})->dailyAt(SystemSetting::get('backup_cleanup_time', '03:00'))->name('backup-cleanup');

Schedule::call(function (AuditService $auditService) {
    $auditService->archiveOlderThan(SystemSetting::int('audit_archive_days', 90));
})->dailyAt(SystemSetting::get('audit_archive_time', '03:30'))
    ->when(fn () => SystemSetting::bool('audit_archive_enabled', true))
    ->name('audit-log-archive');

Schedule::call(function () {
    User::onlyTrashed()
        ->where('deleted_at', '<', now()->subDays(30))
        ->forceDelete();
})->dailyAt('03:45')->name('user-soft-delete-gc');

// Automated Reports
Schedule::call(function (ReportService $reportService) {
    $reportService->runScheduledReports('daily');
})->dailyAt('00:01')->name('daily-report');

Schedule::call(function (ReportService $reportService) {
    $reportService->runScheduledReports('weekly');
})->weeklyOn(1, '00:05')->name('weekly-report');

Schedule::call(function (ReportService $reportService) {
    $reportService->runScheduledReports('monthly');
})->monthlyOn(1, '00:10')->name('monthly-report');
