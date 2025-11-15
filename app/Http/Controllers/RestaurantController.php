<?php

namespace App\Http\Controllers;

use App\Models\Network;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    /**
     * Display the specified restaurant within a network context.
     */
    public function show(Network $network, Restaurant $restaurant)
    {
        // Verificar que el usuario es miembro de la red
        if (!$network->hasMember(auth()->user())) {
            abort(403, 'No tienes acceso a esta red.');
        }

        // Cargar las reviews de este restaurante para esta red específica
        $reviews = $restaurant->reviewsForNetwork($network)
            ->with(['user', 'photos', 'prices'])
            ->get();

        // Calcular estadísticas
        $avgRatings = [
            'overall' => $reviews->avg('rating') ?? 0,
            'quality' => $reviews->avg('quality_rating') ?? 0,
            'hospitality' => $reviews->avg('hospitality_rating') ?? 0,
            'service' => $reviews->avg('service_rating') ?? 0,
            'pricing' => $reviews->avg('pricing_rating') ?? 0,
        ];

        // Obtener todas las fotos de las reviews de los miembros
        $allPhotos = $reviews->flatMap(function ($review) {
            return $review->photos;
        });

        // Obtener todos los precios de las reviews
        $allPrices = $reviews->flatMap(function ($review) {
            return $review->prices->map(function ($price) use ($review) {
                $price->member = $review->user;
                return $price;
            });
        });

        $memberRole = $network->getMemberRole(auth()->user());

        return view('restaurants.show', compact(
            'network',
            'restaurant',
            'reviews',
            'avgRatings',
            'allPhotos',
            'allPrices',
            'memberRole'
        ));
    }
}
