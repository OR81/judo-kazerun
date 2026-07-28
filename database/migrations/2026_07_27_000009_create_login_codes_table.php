<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * کدهای یک‌بارمصرف ورود.
     *
     * The code is stored hashed, never in clear: this table is the whole
     * credential store now, and a leaked backup must not hand out live sessions.
     *
     * Rows are kept after use rather than deleted — `consumed_at` makes replay
     * detectable, and the history is what the throttle counts against. A scheduled
     * `auth:prune-login-codes` clears anything older than a day.
     */
    public function up(): void
    {
        Schema::create('login_codes', function (Blueprint $table) {
            $table->id();

            // Not a foreign key: a code may be issued for a number that turns out
            // to have no account, and those attempts still need rate limiting.
            $table->string('mobile', 11)->index();

            $table->string('code_hash');
            $table->timestamp('expires_at');
            $table->timestamp('consumed_at')->nullable();
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->string('ip', 45)->nullable();
            $table->timestamps();

            $table->index(['mobile', 'consumed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_codes');
    }
};
