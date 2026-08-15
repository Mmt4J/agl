<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Powers: Home hero "Register of Divisions", Services page (Schedule A),
 * Services preview grid, and the Quote form's service picker.
 * `blurb` = short hero-panel copy. `description` = full services-page copy.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();      // e.g. "A.1"
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('short_description');        // used on Home services preview cards
            $table->string('blurb');                     // used in the hero Registry Index panel
            $table->text('description');                 // full copy on the Services detail panel
            $table->string('icon');                       // inline SVG markup or icon key
            $table->boolean('is_featured')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
