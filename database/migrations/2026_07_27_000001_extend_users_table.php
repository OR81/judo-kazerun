<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Three portals share one users table, separated by role.
            $table->string('role', 20)->default('athlete')->after('name')->index();

            // The whole identity of an account. Normalised to 09xxxxxxxxx on the
            // model, so the sign-in lookup is a plain equality match.
            $table->string('mobile', 11)->unique()->after('role');

            $table->string('national_code', 10)->nullable()->unique()->after('mobile');
            $table->date('birth_date')->nullable()->after('national_code');
            $table->string('gender', 10)->nullable()->after('birth_date');
            $table->string('avatar')->nullable()->after('gender');
            $table->string('city')->nullable()->after('avatar');
            $table->boolean('is_active')->default(true)->after('city');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role', 'mobile', 'national_code', 'birth_date',
                'gender', 'avatar', 'city', 'is_active', 'last_login_at',
            ]);
        });
    }
};
