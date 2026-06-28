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
            $table->dropUnique(['need_id', 'contributor_id']);
        });
    }

    public function down(): void
    {
        Schema::table('need_commitments', function (Blueprint $table) {
            $table->unique(['need_id', 'contributor_id']);
        });
    }
};
