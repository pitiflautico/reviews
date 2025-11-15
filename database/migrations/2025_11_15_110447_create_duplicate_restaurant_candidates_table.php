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
        Schema::create('duplicate_restaurant_candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_a_id')->constrained('restaurants')->onDelete('cascade');
            $table->foreignId('restaurant_b_id')->constrained('restaurants')->onDelete('cascade');

            // Datos de similitud
            $table->decimal('similarity_score', 5, 2)->comment('Score 0-100 de similitud');
            $table->string('detection_method', 50)->comment('exact_name, similar_name, location_name, etc.');

            // Estado
            $table->enum('status', ['pending', 'confirmed_duplicate', 'not_duplicate', 'merged'])->default('pending');
            $table->foreignId('merged_into_id')->nullable()->constrained('restaurants')->onDelete('set null');
            $table->timestamp('merged_at')->nullable();

            $table->timestamps();

            // Índices
            $table->index('restaurant_a_id');
            $table->index('restaurant_b_id');
            $table->index('similarity_score');
            $table->index('status');
            $table->index('detection_method');

            // Constraint: evitar duplicados del mismo par
            $table->unique(['restaurant_a_id', 'restaurant_b_id'], 'unique_candidate_pair');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('duplicate_restaurant_candidates');
    }
};
