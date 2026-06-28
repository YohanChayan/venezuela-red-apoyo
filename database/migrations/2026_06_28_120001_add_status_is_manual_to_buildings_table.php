<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buildings', function (Blueprint $table) {
            // When false, the status is auto-derived from the building's needs
            // and structural condition; when true, a person set it manually.
            $table->boolean('status_is_manual')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->dropColumn('status_is_manual');
        });
    }
};
