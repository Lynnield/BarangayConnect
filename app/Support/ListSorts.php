<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;

class ListSorts
{
    /** @return array<string, string|\Closure(Builder, string): Builder> */
    public static function documentRequests(): array
    {
        return [
            'created_at' => 'created_at',
            'request_number' => 'request_number',
            'status' => 'status',
            'resident' => fn (Builder $query, string $direction) => $query
                ->join('residents', 'document_requests.resident_id', '=', 'residents.id')
                ->orderBy('residents.full_name', $direction)
                ->select('document_requests.*'),
            'type' => fn (Builder $query, string $direction) => $query
                ->join('document_types', 'document_requests.document_type_id', '=', 'document_types.id')
                ->orderBy('document_types.name', $direction)
                ->select('document_requests.*'),
        ];
    }

    /** @return array<string, string|\Closure(Builder, string): Builder> */
    public static function residents(): array
    {
        return [
            'full_name' => 'full_name',
            'resident_number' => 'resident_number',
            'gender' => 'gender',
            'created_at' => 'created_at',
        ];
    }

    /** @return array<string, string|\Closure(Builder, string): Builder> */
    public static function users(): array
    {
        return [
            'name' => 'name',
            'email' => 'email',
            'status' => 'status',
            'created_at' => 'created_at',
        ];
    }

    /** @return array<string, string|\Closure(Builder, string): Builder> */
    public static function appointments(): array
    {
        return [
            'appointment_date' => fn (Builder $query, string $direction) => $query
                ->orderBy('appointment_date', $direction)
                ->orderBy('appointment_time', $direction),
            'appointment_time' => 'appointment_time',
            'status' => 'status',
            'created_at' => 'created_at',
            'resident' => fn (Builder $query, string $direction) => $query
                ->join('residents', 'appointments.resident_id', '=', 'residents.id')
                ->orderBy('residents.full_name', $direction)
                ->select('appointments.*'),
        ];
    }

    /** @return array<string, string|\Closure(Builder, string): Builder> */
    public static function appointmentSlots(): array
    {
        return [
            'slot_date' => fn (Builder $query, string $direction) => $query
                ->orderBy('slot_date', $direction)
                ->orderBy('slot_time', $direction),
            'slot_time' => 'slot_time',
            'max_appointments' => 'max_appointments',
        ];
    }

    /** @return array<string, string|\Closure(Builder, string): Builder> */
    public static function auditLogs(): array
    {
        return [
            'created_at' => 'created_at',
            'module' => 'module',
            'action' => 'action',
        ];
    }

    /** @return array<string, string|\Closure(Builder, string): Builder> */
    public static function documentTypes(): array
    {
        return [
            'name' => 'name',
            'fee' => 'fee',
            'processing_days' => 'processing_days',
        ];
    }

    /** @return array<string, string|\Closure(Builder, string): Builder> */
    public static function roles(): array
    {
        return [
            'name' => 'name',
            'users' => 'users_count',
            'created_at' => 'created_at',
        ];
    }

    /** @return array<string, string|\Closure(Builder, string): Builder> */
    public static function backups(): array
    {
        return [
            'created_at' => 'created_at',
            'backup_name' => 'backup_name',
            'file_size' => 'file_size',
            'status' => 'status',
        ];
    }

    /** @return array<string, string|\Closure(Builder, string): Builder> */
    public static function reports(): array
    {
        return [
            'created_at' => 'created_at',
            'report_name' => 'report_name',
            'report_type' => 'report_type',
            'status' => 'status',
        ];
    }
}
