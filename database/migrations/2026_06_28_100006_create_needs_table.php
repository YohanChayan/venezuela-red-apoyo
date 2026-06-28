<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Needs are "parcelled": each is its own row so two people editing
     * different needs of the same building never collide.
     */
    public function up(): void
    {
        Schema::create('needs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained()->cascadeOnDelete();
            // Either a predefined supply...
            $table->foreignId('supply_id')->nullable()->constrained()->nullOnDelete();
            // ...or a free-text supply, always categorised for reporting.
            $table->foreignId('supply_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('custom_supply_name')->nullable();

            $table->decimal('quantity', 12, 2)->nullable();
            $table->string('unit')->nullable();
            $table->string('priority')->default('media')->index();
            $table->string('status')->default('solicitada')->index();
            $table->text('notes')->nullable();

            // Lifecycle attribution (anti-chaos).
            $table->foreignId('claimed_by_contributor_id')->nullable()->constrained('contributors')->nullOnDelete();
            $table->timestamp('claimed_at')->nullable();
            $table->timestamp('fulfilled_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();

            $table->unsignedInteger('version')->default(0);
            $table->string('created_by')->nullable();
            $table->timestamp('last_reported_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['building_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('needs');
    }
};
