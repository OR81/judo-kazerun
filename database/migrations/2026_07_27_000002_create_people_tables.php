<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // کمربندها — white through black, then dan grades.
        Schema::create('belts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('color', 9);
            $table->unsignedTinyInteger('dan_level')->nullable();
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });

        // مربیان — a coach is publishable content first; the portal login is optional,
        // so user_id stays nullable and detaching an account never deletes the profile.
        Schema::create('coaches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('belt_id')->nullable()->constrained()->nullOnDelete();

            $table->string('name');
            $table->string('slug')->unique();
            $table->string('photo')->nullable();
            $table->string('title')->nullable();
            $table->unsignedTinyInteger('dan_rank')->nullable();
            $table->text('summary')->nullable();
            $table->longText('bio')->nullable();
            $table->unsignedSmallInteger('experience_years')->default(0);

            $table->json('specialties')->nullable();
            $table->json('certificates')->nullable();

            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('instagram')->nullable();

            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'order']);
        });

        // ورزشکاران
        Schema::create('athletes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('belt_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('coach_id')->nullable()->constrained('coaches')->nullOnDelete();

            $table->string('name');
            $table->string('slug')->unique();
            $table->string('photo')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('gender', 10)->default('male');
            $table->string('weight_class', 20)->nullable();
            $table->string('club')->nullable();
            $table->string('city')->nullable();
            $table->text('bio')->nullable();

            $table->boolean('is_national_team')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();

            $table->index(['is_national_team', 'is_active']);
        });

        // افتخارات و مدال‌ها
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('athlete_id')->constrained()->cascadeOnDelete();

            $table->string('title');
            $table->string('competition');
            $table->string('rank', 20)->default('gold');
            $table->string('level', 20)->default('provincial');
            $table->unsignedSmallInteger('year');
            $table->date('achieved_at')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['athlete_id', 'year']);
        });

        // هیئت‌رئیسه و کمیته‌ها
        Schema::create('board_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('position', 40);
            $table->string('committee')->nullable();
            $table->string('photo')->nullable();
            $table->text('summary')->nullable();
            $table->longText('bio')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('board_members');
        Schema::dropIfExists('achievements');
        Schema::dropIfExists('athletes');
        Schema::dropIfExists('coaches');
        Schema::dropIfExists('belts');
    }
};
