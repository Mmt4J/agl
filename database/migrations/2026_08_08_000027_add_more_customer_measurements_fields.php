<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_measurements', function (Blueprint $table) {
            // Upper body additions
            $table->decimal('top_length', 6, 2)->nullable();
            $table->decimal('half_bust', 6, 2)->nullable();
            $table->decimal('half_length', 6, 2)->nullable();
            $table->decimal('round_sleeve', 6, 2)->nullable();
            $table->decimal('length_sleeve', 6, 2)->nullable();

            // Lower body additions
            $table->decimal('trouser_skirt_length', 6, 2)->nullable();

            // Overall additions
            $table->decimal('gown_length', 6, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('customer_measurements', function (Blueprint $table) {
            $table->dropColumn([
                'top_length',
                'half_bust',
                'half_length',
                'round_sleeve',
                'length_sleeve',
                'trouser_skirt_length',
                'gown_length',
            ]);
        });
    }
};
