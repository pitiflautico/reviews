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
        Schema::table('restaurants', function (Blueprint $table) {
            // Description
            $table->text('description')->nullable()->after('website');

            // Status
            $table->enum('status', ['open', 'closed', 'temporarily_closed'])->default('open')->after('description');

            // Email
            $table->string('email')->nullable()->after('phone');

            // Tags (JSON field for categories/tags)
            $table->json('tags')->nullable()->after('description');

            // Amenities (JSON field)
            $table->json('amenities')->nullable()->after('tags');

            // Social media
            $table->string('facebook_url')->nullable()->after('website');
            $table->string('instagram_url')->nullable()->after('facebook_url');
            $table->string('twitter_url')->nullable()->after('instagram_url');

            // Logo/Image
            $table->string('logo_url')->nullable()->after('instagram_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'status',
                'email',
                'tags',
                'amenities',
                'facebook_url',
                'instagram_url',
                'twitter_url',
                'logo_url',
            ]);
        });
    }
};
