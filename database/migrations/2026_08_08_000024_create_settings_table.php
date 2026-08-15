<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Simple key-value store for company-profile facts that appear across many
 * pages (footer, trust strip, contact page): RC number, SCUML number,
 * phone numbers, WhatsApp number, email, address, social links. Keeps those
 * values editable from the back office instead of hard-coded in Blade.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();      // e.g. "company.rc_number"
            $table->text('value')->nullable();
            $table->enum('type', ['string', 'number', 'boolean', 'json'])->default('string');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
