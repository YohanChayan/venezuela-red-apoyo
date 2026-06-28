<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drop the one-commitment-per-contributor rule: a single device (one
     * contributor) may register several named helpers for the same need
     * (e.g. a coordinator anotando varias personas/brigadas). Commitments are
     * now de-duplicated by name in the service, not by a DB constraint.
     */
    public function up(): void
    {
        Schema::table('need_commitments', function (Blueprint $table) {
            // MySQL backs the `need_id` foreign key with the composite unique
            // index (need_id is its leftmost column), so it refuses to drop the
            // index while the FK exists. Give the FK its own index first.
            $table->index('need_id');
            $table->dropUnique(['need_id', 'contributor_id']);
        });
    }

    public function down(): void
    {
        Schema::table('need_commitments', function (Blueprint $table) {
            // Restore the composite unique (which again covers the need_id FK),
            // then drop the standalone index added in up().
            $table->unique(['need_id', 'contributor_id']);
            $table->dropIndex(['need_id']);
        });
    }
};
