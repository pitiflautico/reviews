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
        Schema::create('visit_wishes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('network_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');

            // Datos de la wishlist
            $table->text('notes')->nullable();
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->boolean('visited')->default(false);
            $table->timestamp('visited_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('network_id');
            $table->index('user_id');
            $table->index('restaurant_id');
            $table->index('visited');
            $table->index(['network_id', 'user_id'], 'idx_network_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visit_wishes');
    }
};
