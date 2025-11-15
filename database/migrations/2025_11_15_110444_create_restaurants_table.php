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
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();

            // Datos básicos
            $table->string('name');
            $table->string('name_normalized')->comment('Nombre normalizado para búsquedas y detección de duplicados');
            $table->string('slug')->unique();

            // Ubicación
            $table->string('address', 500)->nullable();
            $table->string('address_normalized', 500)->nullable()->comment('Dirección normalizada para comparaciones');
            $table->string('city', 100)->nullable();
            $table->string('country', 100)->default('España');
            $table->string('postal_code', 20)->nullable();

            // Geolocalización
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Información adicional
            $table->string('cuisine_type', 100)->nullable();
            $table->enum('price_range', ['low', 'medium', 'high', 'very_high'])->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('website')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Índices para sistema global
            $table->index('name_normalized', 'idx_name_normalized');
            $table->index('address_normalized', 'idx_address_normalized');
            $table->index(['latitude', 'longitude'], 'idx_location');
            $table->index('cuisine_type', 'idx_cuisine');
            $table->index('city', 'idx_city');
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};
