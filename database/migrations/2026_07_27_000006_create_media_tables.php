<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // آلبوم‌های گالری
        Schema::create('gallery_albums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->nullable()->constrained()->nullOnDelete();

            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('type', 20)->default('photo');
            $table->date('taken_on')->nullable();

            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::create('gallery_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gallery_album_id')->constrained()->cascadeOnDelete();

            $table->string('type', 20)->default('photo');
            $table->string('path');
            $table->string('thumbnail')->nullable();
            $table->string('caption')->nullable();
            // Kept so the masonry grid can reserve space and avoid layout shift.
            $table->unsignedSmallInteger('width')->nullable();
            $table->unsignedSmallInteger('height')->nullable();
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });

        // فایل‌های قابل دانلود — فرم، آیین‌نامه، جزوه
        Schema::create('downloads', function (Blueprint $table) {
            $table->id();
            $table->string('category', 30)->default('form');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            $table->string('file_path');
            $table->string('file_name');
            $table->string('extension', 10)->nullable();
            $table->unsignedInteger('size')->default(0);
            $table->unsignedInteger('downloads_count')->default(0);

            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();

            $table->index(['category', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('downloads');
        Schema::dropIfExists('gallery_items');
        Schema::dropIfExists('gallery_albums');
    }
};
