<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lightweight identity layer: one row per device token. No registration,
     * but every public edit is attributable and revocable (ban / trust).
     */
    public function up(): void
    {
        Schema::create('contributors', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            // banned | normal | trusted | moderator | admin
            $table->string('trust_level')->default('normal')->index();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('first_seen_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contributors');
    }
};
