<?php

namespace App\Observers;

use App\Models\Restaurant;
use App\Services\RestaurantNormalizationService;

class RestaurantObserver
{
    public function __construct(
        private RestaurantNormalizationService $normalizationService
    ) {}

    /**
     * Handle the Restaurant "saving" event.
     *
     * Normaliza name y address ANTES de guardar
     */
    public function saving(Restaurant $restaurant): void
    {
        // Normalizar nombre si cambió
        if ($restaurant->isDirty('name')) {
            $restaurant->name_normalized = $this->normalizationService->normalizeName($restaurant->name);
        }

        // Normalizar dirección si cambió y existe
        if ($restaurant->isDirty('address') && !empty($restaurant->address)) {
            $restaurant->address_normalized = $this->normalizationService->normalizeAddress($restaurant->address);
        }
    }
}
