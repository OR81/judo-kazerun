<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // اسلایدهای صفحهٔ اصلی
        Schema::create('sliders', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->string('image');
            $table->string('eyebrow')->nullable();

            $table->string('cta_label')->nullable();
            $table->string('cta_url')->nullable();
            $table->string('secondary_cta_label')->nullable();
            $table->string('secondary_cta_url')->nullable();

            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });

        // حامیان
        Schema::create('sponsors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('logo');
            $table->string('url')->nullable();
            $table->string('tier', 20)->default('partner');
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });

        // تنظیمات سایت — آدرس، تلفن، شبکه‌های اجتماعی، ساعات کاری
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group', 40)->default('general');
            $table->string('type', 20)->default('string');
            $table->timestamps();
        });

        // پیام‌های فرم تماس
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('subject');
            $table->text('message');

            $table->boolean('is_read')->default(false);
            $table->timestamp('replied_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['is_read', 'created_at']);
        });

        // مشترکان خبرنامه
        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            // Used for the one-click unsubscribe link.
            $table->string('token', 64)->unique();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_subscribers');
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('sponsors');
        Schema::dropIfExists('sliders');
    }
};
