<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('color', 20)->default('brand');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('body');
            $table->string('cover_image')->nullable();

            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('views')->default(0);
            $table->unsignedSmallInteger('read_minutes')->default(3);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            // The archive is always "published, newest first".
            $table->index(['published_at', 'is_featured']);
        });

        // رویدادها — مسابقه، آزمون دان، اردو، سمینار
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('type', 30)->default('competition');
            $table->text('summary')->nullable();
            $table->longText('description')->nullable();
            $table->string('poster')->nullable();

            $table->string('location')->nullable();
            $table->string('organizer')->nullable();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at')->nullable();
            $table->dateTime('registration_deadline')->nullable();

            $table->unsignedSmallInteger('capacity')->nullable();
            $table->unsignedInteger('fee')->nullable();
            $table->string('age_groups')->nullable();
            $table->string('status', 20)->default('scheduled');
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index(['starts_at', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
        Schema::dropIfExists('news');
        Schema::dropIfExists('news_categories');
    }
};
