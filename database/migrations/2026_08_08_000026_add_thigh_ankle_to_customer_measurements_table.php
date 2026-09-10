<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_measurements', function (Blueprint $table) {
            $table->decimal('thigh', 6, 2)->nullable();
            $table->decimal('ankle', 6, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('customer_measurements', function (Blueprint $table) {
            $table->dropColumn(['thigh', 'ankle']);
        });
    }
};
