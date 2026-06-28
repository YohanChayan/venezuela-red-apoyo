<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Catalog of predefined supplies. Free-text supplies are NOT stored here;
     * they live on the need itself (needs.custom_supply_name).
     */
    public function up(): void
    {
        Schema::create('supplies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supply_category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('unit')->nullable();
            $table->boolean('is_predefined')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('supply_category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplies');
    }
};
