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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('network_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');

            // Datos de la reseña
            $table->unsignedTinyInteger('rating')->comment('Calificación de 1 a 5');
            $table->text('comment')->nullable();
            $table->date('date_of_visit')->nullable();
            $table->enum('meal_type', ['breakfast', 'lunch', 'dinner'])->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('network_id');
            $table->index('user_id');
            $table->index('restaurant_id');
            $table->index('rating');
            $table->index('date_of_visit');
            $table->index(['network_id', 'restaurant_id'], 'idx_network_restaurant');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
