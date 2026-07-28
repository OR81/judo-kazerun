<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // سالن‌های خانهٔ جودو — the halls the board owns and operates itself
        Schema::create('venues', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->unique();
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();

            $table->unsignedSmallInteger('tatami_area')->default(0);   // متر مربع
            $table->unsignedSmallInteger('capacity')->default(0);      // نفر، هم‌زمان
            $table->unsignedInteger('session_rate')->default(0);       // تومان، به ازای هر سانس

            // امکانات — a plain list of Persian labels, rendered as chips
            $table->json('features')->nullable();

            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });

        /*
         * سانس‌ها — the recurring weekly slots each hall is divided into.
         *
         * This is the board's actual business: a slot is either run by the board
         * as one of its own classes, rented out to a club or group, free to rent,
         * or closed for maintenance. day_of_week: 0 = شنبه … 6 = جمعه, matching
         * training_sessions so both timetables can be merged.
         */
        Schema::create('venue_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_id')->constrained()->cascadeOnDelete();

            // Set when the slot is one of the board's own classes, so the hall
            // board can link straight through to the class and its coach.
            $table->foreignId('training_class_id')->nullable()->constrained()->nullOnDelete();

            $table->unsignedTinyInteger('day_of_week');
            $table->time('start_time');
            $table->time('end_time');

            $table->string('status', 20)->default('open');
            $table->string('gender', 10)->default('mixed');

            // نام باشگاه یا گروه رزروکننده — free text, no account required
            $table->string('holder')->nullable();

            // Overrides the hall's session_rate for this slot; null means inherit.
            $table->unsignedInteger('price')->nullable();
            $table->string('note')->nullable();

            $table->timestamps();

            $table->index(['day_of_week', 'start_time']);
            $table->index(['status', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venue_slots');
        Schema::dropIfExists('venues');
    }
};
