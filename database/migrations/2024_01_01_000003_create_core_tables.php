<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('residents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('resident_number')->unique()->nullable();
            $table->string('full_name');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->date('birthdate');
            $table->enum('civil_status', ['single', 'married', 'widowed', 'separated', 'divorced']);
            $table->text('address');
            $table->string('contact_number')->nullable();
            $table->string('email')->nullable();
            $table->string('occupation')->nullable();
            $table->string('valid_id_type')->nullable();
            $table->string('valid_id_number')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->json('required_fields')->nullable();
            $table->json('required_attachments')->nullable();
            $table->decimal('fee', 8, 2)->default(0);
            $table->integer('processing_days')->default(1);
            $table->boolean('is_active')->default(true);
            $table->string('template_path')->nullable();
            $table->timestamps();
        });

        Schema::create('document_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->foreignId('resident_id')->constrained()->cascadeOnDelete();
            $table->foreignId('document_type_id')->constrained()->cascadeOnDelete();
            $table->enum('status', [
                'pending', 'under_review', 'for_revision', 'approved',
                'rejected', 'ready_for_pickup', 'released', 'cancelled'
            ])->default('pending');
            $table->text('purpose');
            $table->json('form_data')->nullable();
            $table->text('remarks')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('revision_notes')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->string('pdf_path')->nullable();
            $table->decimal('fee_amount', 8, 2)->default(0);
            $table->boolean('fee_paid')->default(false);
            $table->timestamp('fee_paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('request_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_request_id')->constrained()->cascadeOnDelete();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->string('attachment_type')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('request_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_request_id')->constrained()->cascadeOnDelete();
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->text('notes')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('appointment_number')->unique();
            $table->foreignId('document_request_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('resident_id')->constrained()->cascadeOnDelete();
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->enum('status', ['scheduled', 'confirmed', 'rescheduled', 'completed', 'cancelled', 'no_show'])->default('scheduled');
            $table->text('notes')->nullable();
            $table->foreignId('managed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reminder_sent_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('appointment_slots', function (Blueprint $table) {
            $table->id();
            $table->date('slot_date');
            $table->time('slot_time');
            $table->integer('max_appointments')->default(5);
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            $table->unique(['slot_date', 'slot_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_slots');
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('request_status_logs');
        Schema::dropIfExists('request_attachments');
        Schema::dropIfExists('document_requests');
        Schema::dropIfExists('document_types');
        Schema::dropIfExists('residents');
    }
};
