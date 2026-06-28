<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type')->default('otro')->index();
            $table->string('address')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();

            $table->string('status')->default('normal')->index();
            $table->string('mode')->default('abastecimiento')->index();
            $table->string('structural_status')->default('sin_evaluar');

            $table->unsignedInteger('people_trapped_estimate')->nullable();
            $table->unsignedInteger('people_evacuated')->nullable();
            $table->unsignedInteger('residents_estimate')->nullable();

            $table->string('contact_name')->nullable();
            $table->string('contact_phone')->nullable();
            // Link out to the existing missing-persons site for this address.
            $table->string('external_persons_url')->nullable();
            $table->text('notes')->nullable();

            // Provenance for seeded data.
            $table->string('source_url')->nullable();
            $table->string('confidence')->nullable();

            // Emergency-brake controls (admin only).
            $table->boolean('is_locked')->default(false);
            $table->json('locked_fields')->nullable();

            // Optimistic concurrency control.
            $table->unsignedInteger('version')->default(0);

            $table->timestamp('last_reported_at')->nullable();
            $table->string('last_reported_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['lat', 'lng']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buildings');
    }
};
