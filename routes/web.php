<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\MfaChallengeController;
use App\Http\Controllers\Account\SecurityController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Staff;
use App\Http\Controllers\Resident;

// Public routes
Route::get('/', function () {
    return view('landing');
})->name('home');

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

    Route::get('/login/mfa', [MfaChallengeController::class, 'show'])->name('login.mfa');
    Route::post('/login/mfa', [MfaChallengeController::class, 'verify'])->middleware('throttle:20,1');
    Route::post('/login/mfa/resend-email', [MfaChallengeController::class, 'resendEmail'])->middleware('throttle:6,1');
    Route::post('/login/mfa/cancel', [MfaChallengeController::class, 'cancel'])->name('login.mfa.cancel');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->prefix('account')->name('account.')->group(function () {
    Route::get('security', [SecurityController::class, 'index'])->name('security.index');
    Route::post('security/totp/start', [SecurityController::class, 'startTotpEnrollment'])->name('security.totp.start');
    Route::post('security/totp/cancel', [SecurityController::class, 'cancelTotpEnrollment'])->name('security.totp.cancel');
    Route::post('security/totp/confirm', [SecurityController::class, 'confirmTotpEnrollment'])->name('security.totp.confirm');

    Route::post('security/email/send', [SecurityController::class, 'sendEnrollmentEmail'])->name('security.email.send');
    Route::post('security/email/confirm', [SecurityController::class, 'confirmEnrollmentEmail'])->name('security.email.confirm');

    Route::post('security/disable/email', [SecurityController::class, 'sendDisableEmailCode'])
        ->middleware('throttle:10,1')
        ->name('security.disable.email');
    Route::post('security/disable', [SecurityController::class, 'disable'])
        ->middleware('throttle:10,1')
        ->name('security.disable');

    Route::post('security/recovery/email', [SecurityController::class, 'sendRecoveryRegenerationEmail'])
        ->middleware('throttle:10,1')
        ->name('security.recovery.email');
    Route::post('security/recovery/regenerate', [SecurityController::class, 'regenerateRecoveryCodes'])
        ->middleware('throttle:10,1')
        ->name('security.recovery.regenerate');

    Route::post('security/password', [SecurityController::class, 'updatePassword'])->name('security.password');
});

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/chart-data', [Admin\DashboardController::class, 'chartData'])->name('dashboard.chart-data');

    // Users
    Route::resource('users', Admin\UserController::class);
    Route::post('users/{user}/impersonate', [Admin\ImpersonationController::class, 'impersonate'])->name('users.impersonate');
    Route::post('users/stop-impersonation', [Admin\ImpersonationController::class, 'leave'])->name('users.stop-impersonation');
    Route::post('users/{user}/force-logout', [Admin\UserController::class, 'forceLogout'])->name('users.force-logout');
    Route::post('users/{user}/reset-password', [Admin\UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::get('users/{user}/login-history', [Admin\UserController::class, 'loginHistory'])->name('users.login-history');

    // Roles & Permissions
    Route::resource('roles', Admin\RoleController::class);
    Route::post('roles/{role}/permissions', [Admin\RoleController::class, 'updatePermissions'])->name('roles.permissions');

    // Residents (import/export before resource to avoid route conflicts)
    Route::get('residents/import', [Admin\ResidentController::class, 'importForm'])->name('residents.import-form');
    Route::post('residents/import/preview', [Admin\ResidentController::class, 'previewImport'])->name('residents.import-preview');
    Route::post('residents/import', [Admin\ResidentController::class, 'import'])->name('residents.import');
    Route::get('residents/import-template', [Admin\ResidentController::class, 'downloadTemplate'])->name('residents.import-template');
    Route::get('residents/export', [Admin\ResidentController::class, 'export'])->name('residents.export');
    Route::resource('residents', Admin\ResidentController::class);

    // Document Types
    Route::resource('document-types', Admin\DocumentTypeController::class);

    // Document Requests
    Route::get('requests', [Admin\RequestController::class, 'index'])->name('requests.index');
    Route::get('requests/{documentRequest}', [Admin\RequestController::class, 'show'])->name('requests.show');

    // Appointments (static paths before resource)
    Route::get('appointments/calendar', [Admin\AppointmentController::class, 'calendar'])->name('appointments.calendar');
    Route::get('appointments/slots', [Admin\AppointmentController::class, 'slots'])->name('appointments.slots');
    Route::post('appointments/slots', [Admin\AppointmentController::class, 'storeSlot'])->name('appointments.slots.store');
    Route::resource('appointments', Admin\AppointmentController::class);

    // Reports
    Route::get('reports', [Admin\ReportController::class, 'index'])->name('reports.index');
    Route::post('reports/generate', [Admin\ReportController::class, 'generate'])->name('reports.generate');
    Route::get('reports/{report}/download', [Admin\ReportController::class, 'download'])->name('reports.download');

    // Audit Logs
    Route::get('audit-logs', [Admin\AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('audit-logs/export', [Admin\AuditLogController::class, 'export'])->name('audit-logs.export');

    // Settings
    Route::get('settings', [Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings', [Admin\SettingsController::class, 'update'])->name('settings.update');
    Route::post('settings/logo', [Admin\SettingsController::class, 'updateLogo'])->name('settings.logo');

    // Backups
    Route::get('backups', [Admin\BackupController::class, 'index'])->name('backups.index');
    Route::post('backups', [Admin\BackupController::class, 'create'])->name('backups.create');
    Route::get('backups/{backup}/download', [Admin\BackupController::class, 'download'])->name('backups.download');
    Route::delete('backups/{backup}', [Admin\BackupController::class, 'destroy'])->name('backups.destroy');

    // Soft-delete recovery center
    Route::get('trash', [Admin\TrashController::class, 'index'])->name('trash.index');
    Route::post('trash/{type}/{id}/restore', [Admin\TrashController::class, 'restore'])->name('trash.restore');
    Route::delete('trash/{type}/{id}', [Admin\TrashController::class, 'forceDelete'])->name('trash.force-delete');
});

// Staff routes
Route::prefix('staff')->name('staff.')->middleware(['auth', 'role:staff,admin'])->group(function () {
    Route::get('/dashboard', [Staff\DashboardController::class, 'index'])->name('dashboard');

    // Requests management
    Route::get('requests', [Staff\RequestController::class, 'index'])->name('requests.index');
    Route::get('requests/{documentRequest}', [Staff\RequestController::class, 'show'])->name('requests.show');
    Route::post('requests/{documentRequest}/status', [Staff\RequestController::class, 'updateStatus'])->name('requests.update-status');
    Route::get('requests/{documentRequest}/pdf', [Staff\RequestController::class, 'generatePdf'])->name('requests.pdf');
    Route::post('requests/bulk-action', [Staff\RequestController::class, 'bulkAction'])->name('requests.bulk-action');

    // Appointments
    Route::get('appointments/calendar', [Staff\AppointmentController::class, 'calendar'])->name('appointments.calendar');
    Route::post('appointments/{appointment}/confirm', [Staff\AppointmentController::class, 'confirm'])->name('appointments.confirm');
    Route::resource('appointments', Staff\AppointmentController::class);

    // Residents
    Route::post('residents/{resident}/verify', [Staff\ResidentController::class, 'approveVerification'])->name('residents.verify');
    Route::post('residents/{resident}/reject-verification', [Staff\ResidentController::class, 'rejectVerification'])->name('residents.reject-verification');
    Route::resource('residents', Staff\ResidentController::class)->only(['index', 'show', 'edit', 'update']);

    // Reports
    Route::get('reports', [Staff\ReportController::class, 'index'])->name('reports.index');
    Route::post('reports/generate', [Staff\ReportController::class, 'generate'])->name('reports.generate');
});

// Resident routes
Route::prefix('resident')->name('resident.')->middleware(['auth', 'role:resident'])->group(function () {
    Route::get('/dashboard', [Resident\DashboardController::class, 'index'])->name('dashboard');

    // Document requests
    Route::resource('requests', Resident\DocumentRequestController::class)
        ->parameters(['requests' => 'documentRequest']);
    Route::post('requests/{documentRequest}/cancel', [Resident\DocumentRequestController::class, 'cancel'])->name('requests.cancel');
    Route::get('requests/{documentRequest}/download', [Resident\DocumentRequestController::class, 'download'])->name('requests.download');

    // Appointments
    Route::get('appointments', [Resident\AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('appointments/create/{documentRequest?}', [Resident\AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('appointments', [Resident\AppointmentController::class, 'store'])->name('appointments.store');
    Route::get('appointments/{appointment}', [Resident\AppointmentController::class, 'show'])->name('appointments.show');
    Route::post('appointments/{appointment}/reschedule', [Resident\AppointmentController::class, 'reschedule'])->name('appointments.reschedule');
    Route::post('appointments/{appointment}/cancel', [Resident\AppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::get('appointment-slots', [Resident\AppointmentController::class, 'getAvailableSlots'])->name('appointment-slots');

    // Profile
    Route::get('profile', [Resident\ProfileController::class, 'show'])->name('profile.show');
    Route::get('profile/edit', [Resident\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [Resident\ProfileController::class, 'update'])->name('profile.update');
    Route::post('profile/avatar', [Resident\ProfileController::class, 'updateAvatar'])->name('profile.avatar');

    // Notifications
    Route::get('notifications', [Resident\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{notification}/read', [Resident\NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('notifications/read-all', [Resident\NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::delete('notifications/{notification}', [Resident\NotificationController::class, 'destroy'])->name('notifications.destroy');
});

// Shared authenticated notification helpers
Route::middleware('auth')->group(function () {
    Route::get('/notifications/count', function () {
        return response()->json(['count' => auth()->user()->unreadNotifications->count()]);
    })->name('notifications.count');

    Route::get('/notifications/stream', function () {
        return response()->stream(function () {
            for ($i = 0; $i < 6; $i++) {
                echo "event: notification-count\n";
                echo 'data: ' . json_encode(['count' => auth()->user()->unreadNotifications()->count(), 'at' => now()->toIso8601String()]) . "\n\n";
                @ob_flush();
                flush();
                sleep(10);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    })->name('notifications.stream');

    Route::post('/notifications/{id}/read', function (string $id) {
        $n = auth()->user()->notifications()->where('id', $id)->firstOrFail();
        $n->markAsRead();

        return response()->json([
            'success' => true,
            'count' => auth()->user()->unreadNotifications()->count(),
        ]);
    })->name('notifications.mark-read');

    Route::post('/notifications/read-all-global', function () {
        auth()->user()->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    })->name('notifications.read-all-global');
});
