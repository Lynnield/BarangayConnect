<?php

namespace App\Services;

use App\Models\{Appointment, DocumentRequest, User};
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SystemWarningService
{
    /**
     * Get all active system warnings.
     */
    public function getAllWarnings(): Collection
    {
        return collect([
            ...$this->getOverdueAppointments(),
            ...$this->getDelayedRequests(),
            ...$this->getDuplicateRequests(),
            ...$this->getFailedLoginAttempts(),
            ...$this->getUnclaimedDocuments(),
        ])->sortByDesc('severity_score');
    }

    /**
     * Get summary counts for badges.
     */
    public function getWarningCounts(): array
    {
        $warnings = $this->getAllWarnings();
        
        return [
            'total' => $warnings->count(),
            'critical' => $warnings->where('severity', 'critical')->count(),
            'warning' => $warnings->where('severity', 'warning')->count(),
            'info' => $warnings->where('severity', 'info')->count(),
        ];
    }

    protected function getOverdueAppointments(): array
    {
        return Appointment::whereIn('status', ['scheduled', 'confirmed'])
            ->where('appointment_date', '<', today())
            ->get()
            ->map(fn($a) => [
                'type' => 'overdue_appointment',
                'title' => 'Overdue Appointment',
                'message' => "Appointment #{$a->appointment_number} for {$a->resident->full_name} was scheduled for {$a->appointment_date->format('M d, Y')}.",
                'severity' => 'critical',
                'severity_score' => 3,
                'link' => $this->routeFor('appointments.index', ['status' => $a->status]),
                'id' => $a->id,
            ])
            ->toArray();
    }

    protected function getDelayedRequests(): array
    {
        // Requests older than 3 days still in pending/under_review
        return DocumentRequest::whereIn('status', ['pending', 'under_review'])
            ->where('created_at', '<', now()->subDays(3))
            ->get()
            ->map(fn($r) => [
                'type' => 'delayed_request',
                'title' => 'Delayed Request Processing',
                'message' => "Request #{$r->request_number} has been waiting for {$r->created_at->diffForHumans()} without progress.",
                'severity' => 'warning',
                'severity_score' => 2,
                'link' => $this->routeFor('requests.index', ['status' => $r->status]),
                'id' => $r->id,
            ])
            ->toArray();
    }

    protected function getDuplicateRequests(): array
    {
        // Simple detection of multiple pending requests for same type/resident
        $duplicates = DocumentRequest::where('status', 'pending')
            ->select('resident_id', 'document_type_id')
            ->groupBy('resident_id', 'document_type_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        return $duplicates->map(fn($d) => [
            'type' => 'duplicate_request',
            'title' => 'Duplicate Request Detected',
            'message' => "Resident {$d->resident->full_name} has submitted multiple pending requests for {$d->documentType->name}.",
            'severity' => 'info',
            'severity_score' => 1,
            'link' => $this->routeFor('requests.index', ['search' => $d->resident->full_name]),
            'id' => "{$d->resident_id}-{$d->document_type_id}",
        ])->toArray();
    }

    protected function getFailedLoginAttempts(): array
    {
        return User::where('failed_login_attempts', '>', 3)
            ->get()
            ->map(fn($u) => [
                'type' => 'failed_logins',
                'title' => 'Security Alert: Failed Logins',
                'message' => "User {$u->email} has {$u->failed_login_attempts} failed login attempts. Account may be compromised.",
                'severity' => 'critical',
                'severity_score' => 3,
                'link' => $this->userWarningRoute($u),
                'id' => $u->id,
            ])
            ->toArray();
    }

    protected function getUnclaimedDocuments(): array
    {
        // Documents ready for pickup older than 7 days
        return DocumentRequest::where('status', 'ready_for_pickup')
            ->where('processed_at', '<', now()->subDays(7))
            ->get()
            ->map(fn($r) => [
                'type' => 'unclaimed_document',
                'title' => 'Unclaimed Document',
                'message' => "Document #{$r->request_number} has been ready for 7+ days but remains unclaimed.",
                'severity' => 'warning',
                'severity_score' => 2,
                'link' => $this->routeFor('requests.index', ['status' => 'ready_for_pickup']),
                'id' => $r->id,
            ])
            ->toArray();
    }

    protected function routeFor(string $name, array $parameters = []): string
    {
        $user = auth()->user();
        $prefix = $user?->isStaff() ? 'staff' : 'admin';
        $routeName = "{$prefix}.{$name}";

        return route($routeName, $parameters);
    }

    protected function userWarningRoute(User $target): string
    {
        $user = auth()->user();

        if ($user?->isStaff()) {
            return route('staff.dashboard');
        }

        return route('admin.users.index', ['search' => $target->email]);
    }
}
