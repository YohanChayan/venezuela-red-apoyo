<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The trapped-people estimate is no longer tracked per place.
     */
    public function up(): void
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->dropColumn('people_trapped_estimate');
        });
    }

    public function down(): void
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->unsignedInteger('people_trapped_estimate')->nullable()->after('structural_status');
        });
    }
};
