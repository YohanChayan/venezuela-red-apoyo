<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->string('type')->index();
            $table->text('message');
            // Optional way for the user to be reached for follow-up (email/phone).
            $table->string('contact')->nullable();
            // Where the feedback was sent from, plus who (anonymous contributor).
            $table->string('url')->nullable();
            $table->foreignId('contributor_id')->nullable()->constrained()->nullOnDelete();
            $table->string('user_agent', 512)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
