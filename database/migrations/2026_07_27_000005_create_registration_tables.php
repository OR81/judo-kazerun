<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ثبت‌نام‌ها
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            // Registration is open to guests, so the account link is optional.
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('training_class_id')->constrained()->restrictOnDelete();

            // The tracking code the applicant is given; safe to expose in URLs.
            $table->string('reference', 20)->unique();

            $table->string('first_name');
            $table->string('last_name');
            $table->string('national_code', 10);
            $table->string('mobile', 11);
            $table->string('email')->nullable();
            $table->date('birth_date');
            $table->string('gender', 10);

            $table->string('guardian_name')->nullable();
            $table->string('emergency_phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->text('medical_notes')->nullable();
            $table->boolean('has_insurance')->default(false);

            $table->unsignedInteger('amount')->default(0);
            $table->string('status', 25)->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            // One person shouldn't sit in the same class twice.
            $table->unique(['training_class_id', 'national_code']);
        });

        // مدارک بارگذاری‌شده
        Schema::create('enrollment_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained()->cascadeOnDelete();

            $table->string('type', 30);
            $table->string('path');
            $table->string('original_name');
            $table->unsignedInteger('size')->default(0);
            $table->string('mime', 100)->nullable();
            $table->timestamps();
        });

        // تراکنش‌های پرداخت
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained()->cascadeOnDelete();

            $table->string('gateway', 30)->default('fake');
            // Stored in Toman, which is what the board and applicants deal in.
            $table->unsignedInteger('amount');
            $table->string('authority')->nullable()->index();
            $table->string('ref_id')->nullable();
            $table->string('card_pan', 30)->nullable();
            $table->string('status', 20)->default('pending');
            $table->text('message')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('enrollment_documents');
        Schema::dropIfExists('enrollments');
    }
};
