<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A need-level cancellation closes the whole need (and all its commitments)
     * and stops it from auto-reopening to "solicitada".
     */
    public function up(): void
    {
        Schema::table('needs', function (Blueprint $table) {
            $table->timestamp('cancelled_at')->nullable()->after('confirmed_at');
        });
    }

    public function down(): void
    {
        Schema::table('needs', function (Blueprint $table) {
            $table->dropColumn('cancelled_at');
        });
    }
};
