<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            if (! Schema::hasColumn('residents', 'first_name')) {
                $table->string('first_name')->nullable()->after('full_name');
                $table->string('middle_name')->nullable()->after('first_name');
                $table->string('last_name')->nullable()->after('middle_name');
                $table->string('suffix')->nullable()->after('last_name');
                $table->string('house_number')->nullable()->after('address');
                $table->string('street')->nullable()->after('house_number');
                $table->string('purok')->nullable()->after('street');
                $table->string('barangay')->nullable()->after('purok');
                $table->string('city')->nullable()->after('barangay');
                $table->string('province')->nullable()->after('city');
                $table->string('postal_code')->nullable()->after('province');
                $table->string('verification_status')->default('pending')->after('notes');
                $table->foreignId('verified_by')->nullable()->after('verification_status')->constrained('users')->nullOnDelete();
                $table->timestamp('verified_at')->nullable()->after('verified_by');
            }
        });

        Schema::table('document_types', function (Blueprint $table) {
            if (! Schema::hasColumn('document_types', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        DB::table('residents')->orderBy('id')->chunkById(100, function ($residents) {
            foreach ($residents as $resident) {
                $parts = preg_split('/\s+/', trim((string) $resident->full_name)) ?: [];
                $first = array_shift($parts);
                $last = count($parts) ? array_pop($parts) : null;

                DB::table('residents')
                    ->where('id', $resident->id)
                    ->update([
                        'first_name' => $resident->first_name ?: $first,
                        'middle_name' => $resident->middle_name ?: implode(' ', $parts) ?: null,
                        'last_name' => $resident->last_name ?: $last,
                        'barangay' => $resident->barangay ?: null,
                        'city' => $resident->city ?: null,
                        'province' => $resident->province ?: null,
                        'verification_status' => $resident->verification_status ?: 'pending',
                    ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            if (Schema::hasColumn('residents', 'verified_by')) {
                $table->dropConstrainedForeignId('verified_by');
            }

            foreach ([
                'first_name', 'middle_name', 'last_name', 'suffix',
                'house_number', 'street', 'purok', 'barangay',
                'city', 'province', 'postal_code', 'verification_status',
                'verified_at',
            ] as $column) {
                if (Schema::hasColumn('residents', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('document_types', function (Blueprint $table) {
            if (Schema::hasColumn('document_types', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
