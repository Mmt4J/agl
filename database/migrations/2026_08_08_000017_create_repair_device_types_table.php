<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Lookup table for the Instant Repair Cost Estimator's "Device" dropdown. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repair_device_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // "Android & iPhone", "Laptop & MacBook", ...
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_device_types');
    }
};
