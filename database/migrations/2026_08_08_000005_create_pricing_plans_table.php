<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pricing_category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('tagline');
            // Stored as a display string ("₦180,000", "Custom") rather than decimal,
            // since several plans are quoted, not fixed — see repair_pricing for the
            // structured min/max figures used by the Instant Estimator.
            $table->string('price_label');
            $table->string('period_label');              // "one-time", "starting from", "per month" ...
            $table->boolean('is_highlighted')->default(false); // "Most requested" ribbon
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_plans');
    }
};
