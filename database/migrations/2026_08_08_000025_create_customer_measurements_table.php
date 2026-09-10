<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Measurement records for the fashion design arm of the business.
 *
 * One editable record per customer, identified by a unique customer
 * code (separate from phone, since a family can share one phone).
 * All body measurements are optional decimals — a tailor captures
 * whatever the current fitting needs.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_measurements', function (Blueprint $table) {
            $table->id();
            $table->string('customer_code')->nullable()->unique();
            $table->string('full_name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->enum('unit', ['in', 'cm'])->default('in');

            // Upper body
            $table->decimal('chest', 6, 2)->nullable();
            $table->decimal('bust', 6, 2)->nullable();
            $table->decimal('waist', 6, 2)->nullable();
            $table->decimal('shoulder', 6, 2)->nullable();
            $table->decimal('arm_length', 6, 2)->nullable();

            // Lower body
            $table->decimal('hip', 6, 2)->nullable();
            $table->decimal('inseam', 6, 2)->nullable();

            // Overall
            $table->decimal('height', 6, 2)->nullable();
            $table->decimal('weight', 6, 2)->nullable();
            $table->string('dress_size', 10)->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('full_name');
            $table->index('phone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_measurements');
    }
};
