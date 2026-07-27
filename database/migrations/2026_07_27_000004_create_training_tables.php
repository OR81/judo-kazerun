<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // کلاس‌های تمرینی
        Schema::create('training_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coach_id')->constrained()->cascadeOnDelete();

            $table->string('title');
            $table->string('slug')->unique();
            $table->string('age_group', 20)->default('kids');
            $table->string('gender', 10)->default('mixed');
            $table->string('level', 20)->default('beginner');

            $table->unsignedSmallInteger('capacity')->default(20);
            // Denormalised so the timetable can render a capacity bar without a
            // COUNT per row; kept in step by the enrollment service.
            $table->unsignedSmallInteger('enrolled_count')->default(0);

            $table->unsignedInteger('monthly_fee')->default(0);
            $table->string('venue')->nullable();
            $table->text('description')->nullable();

            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'age_group']);
        });

        // جلسات هفتگی — day_of_week: 0 = شنبه … 6 = جمعه
        Schema::create('training_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_class_id')->constrained()->cascadeOnDelete();

            $table->unsignedTinyInteger('day_of_week');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('venue')->nullable();
            $table->timestamps();

            $table->index(['day_of_week', 'start_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_sessions');
        Schema::dropIfExists('training_classes');
    }
};
