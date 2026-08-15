<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The structured version of the front-end's `repairPriceMatrix` lookup —
 * replaces it entirely once wired to Livewire. Not every device/issue
 * combination needs a row (e.g. "Home Appliance" + "Screen damage" is
 * intentionally absent), matching the prototype's "not a common
 * combination" empty state.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repair_pricing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repair_device_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('repair_issue_type_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('price_min'); // in Naira, minor unit not needed (no kobo pricing shown)
            $table->unsignedInteger('price_max');
            $table->timestamps();

            $table->unique(['repair_device_type_id', 'repair_issue_type_id'], 'repair_pricing_device_issue_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_pricing');
    }
};
