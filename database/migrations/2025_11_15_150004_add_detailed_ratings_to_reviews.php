<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Detailed ratings like in the reference site
            $table->unsignedTinyInteger('quality_rating')->nullable()->after('rating')->comment('Quality rating 1-5');
            $table->unsignedTinyInteger('hospitality_rating')->nullable()->after('quality_rating')->comment('Hospitality rating 1-5');
            $table->unsignedTinyInteger('service_rating')->nullable()->after('hospitality_rating')->comment('Service rating 1-5');
            $table->unsignedTinyInteger('pricing_rating')->nullable()->after('service_rating')->comment('Pricing rating 1-5');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn([
                'quality_rating',
                'hospitality_rating',
                'service_rating',
                'pricing_rating',
            ]);
        });
    }
};
