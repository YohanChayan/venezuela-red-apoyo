<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Many people can commit to the same need ("me encargo"). One row per
     * person (contributor) per need.
     */
    public function up(): void
    {
        Schema::create('need_commitments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('need_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contributor_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name')->nullable();
            $table->timestamps();

            $table->unique(['need_id', 'contributor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('need_commitments');
    }
};
