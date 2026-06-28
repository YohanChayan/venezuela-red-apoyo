<?php

use App\Enums\NeedStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Each commitment now carries its own lifecycle status. A person who
     * commits starts at "comprometida" and advances their own state machine;
     * the need's overall status is derived from the mix of these.
     */
    public function up(): void
    {
        Schema::table('need_commitments', function (Blueprint $table) {
            $table->string('status')->default(NeedStatus::Comprometida->value)->after('name')->index();
        });
    }

    public function down(): void
    {
        Schema::table('need_commitments', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
