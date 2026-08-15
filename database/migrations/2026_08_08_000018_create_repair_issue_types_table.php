<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Lookup table for the Instant Repair Cost Estimator's "Issue" dropdown. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repair_issue_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // "Screen damage", "Battery replacement", ...
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_issue_types');
    }
};
