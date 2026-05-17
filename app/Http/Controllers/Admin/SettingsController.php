<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $this->ensureOperationalDefaults();

        $settings = SystemSetting::orderBy('setting_group')->orderBy('setting_key')->get()->groupBy('setting_group');
        $health = $this->healthSnapshot();

        return view('admin.settings.index', compact('settings', 'health'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable|string|max:10000',
        ]);

        foreach ($request->settings as $key => $value) {
            $setting = SystemSetting::where('setting_key', $key)->first();
            $normalized = is_array($value) ? json_encode($value) : (string) $value;

            if ($setting?->setting_type === 'secret' && $normalized === '********') {
                continue;
            }

            if ($setting) {
                $setting->update(['setting_value' => $normalized]);
            } else {
                SystemSetting::set($key, $normalized);
            }
        }

        AuditService::log('Settings', 'update', null, $request->settings, 'System settings updated');

        return back()->with('success', 'Settings saved.');
    }

    public function updateLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|max:2048',
        ]);

        $path = $request->file('logo')->store('settings', 'public');
        SystemSetting::set('logo_path', $path);
        AuditService::log('Settings', 'logo', null, null, 'Logo updated');

        return back()->with('success', 'Logo updated.');
    }

    private function ensureOperationalDefaults(): void
    {
        $defaults = [
            ['backup_database_enabled', '1', 'backup', 'boolean', 'Enable scheduled database backups.'],
            ['backup_database_time', '01:00', 'backup', 'time', 'Daily database backup time.'],
            ['backup_files_enabled', '1', 'backup', 'boolean', 'Enable scheduled file backups.'],
            ['backup_files_weekday', '0', 'backup', 'integer', 'Weekly file backup day, where 0 is Sunday.'],
            ['backup_files_time', '02:00', 'backup', 'time', 'Weekly file backup time.'],
            ['backup_cleanup_time', '03:00', 'backup', 'time', 'Daily backup cleanup time.'],
            ['audit_archive_enabled', '1', 'audit', 'boolean', 'Archive old audit logs automatically.'],
            ['audit_archive_days', '90', 'audit', 'integer', 'Audit logs older than this many days are archived.'],
            ['audit_archive_time', '03:30', 'audit', 'time', 'Daily audit archive time.'],
            ['single_session_per_user', '0', 'security', 'boolean', 'Invalidate older sessions after a successful login.'],
            ['captcha_after_failed_attempts', '3', 'security', 'integer', 'Show CAPTCHA after this many failed login attempts from the same browser.'],
            ['api_rate_limit_per_minute', '60', 'security', 'integer', 'Default API/request limit budget per minute.'],
            ['sms_enabled', '0', 'sms', 'boolean', 'Enable SMS delivery for MFA and critical alerts.'],
            ['sms_provider', 'log', 'sms', 'string', 'SMS provider key. Supported local default: log.'],
            ['sms_from', 'BarangayConnect', 'sms', 'string', 'Sender label or phone number for SMS.'],
            ['sms_api_key', '', 'sms', 'secret', 'Provider API key. Leave blank for log-only delivery.'],
            ['sms_webhook_url', '', 'sms', 'string', 'Optional webhook endpoint for SMS providers.'],
            ['maintenance_mode', '0', 'system', 'boolean', 'Place the public application in maintenance mode.'],
            ['dark_mode_available', '1', 'accessibility', 'boolean', 'Show the user theme toggle in the top bar.'],
            ['accessibility_statement', 'Barangay Connect supports keyboard navigation, visible focus states, scalable text, and a user-controlled light/dark theme.', 'accessibility', 'text', 'Public accessibility statement.'],
            ['favorite_report_configs', '[]', 'reports', 'json', 'Saved report builder presets.'],
        ];

        foreach ($defaults as [$key, $value, $group, $type, $description]) {
            if (! SystemSetting::where('setting_key', $key)->exists()) {
                SystemSetting::setWithMeta($key, $value, $group, $type, $description);
            }
        }
    }

    private function healthSnapshot(): array
    {
        $dbPath = database_path('database.sqlite');
        $storagePath = storage_path('app');

        return [
            'uptime' => $this->serverUptime(),
            'database_size' => is_file($dbPath) ? $this->formatBytes(filesize($dbPath)) : 'Unavailable',
            'storage_usage' => $this->formatBytes($this->directorySize($storagePath)),
            'php_version' => PHP_VERSION,
            'timezone' => config('app.timezone'),
        ];
    }

    private function serverUptime(): string
    {
        if (PHP_OS_FAMILY === 'Windows') {
            return trim((string) @shell_exec('powershell -NoProfile -Command "(Get-CimInstance Win32_OperatingSystem).LastBootUpTime"')) ?: 'Unavailable';
        }

        $uptime = @file_get_contents('/proc/uptime');
        if (! $uptime) {
            return 'Unavailable';
        }

        $seconds = (int) floor((float) explode(' ', $uptime)[0]);
        return floor($seconds / 86400) . 'd ' . gmdate('H:i:s', $seconds % 86400);
    }

    private function directorySize(string $path): int
    {
        if (! is_dir($path)) {
            return 0;
        }

        $bytes = 0;
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)) as $file) {
            if ($file->isFile()) {
                $bytes += $file->getSize();
            }
        }

        return $bytes;
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $index = 0;
        $size = max($bytes, 0);

        while ($size >= 1024 && $index < count($units) - 1) {
            $size /= 1024;
            $index++;
        }

        return round($size, 2) . ' ' . $units[$index];
    }
}
