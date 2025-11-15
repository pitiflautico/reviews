<?php

namespace App\Http\Controllers;

use App\Models\Network;
use App\Models\Restaurant;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    /**
     * Display a listing of reviews for a network
     */
    public function index(Network $network)
    {
        // Verify user is member of this network
        if (!$network->members->contains(Auth::id())) {
            abort(403, 'No tienes acceso a esta red.');
        }

        $reviews = Review::where('network_id', $network->id)
            ->with(['user', 'restaurant'])
            ->latest()
            ->paginate(12);

        return view('reviews.index', compact('network', 'reviews'));
    }

    /**
     * Show the form for creating a new review
     */
    public function create(Network $network)
    {
        // Verify user is member of this network
        if (!$network->members->contains(Auth::id())) {
            abort(403, 'No tienes acceso a esta red.');
        }

        // Get all restaurants for autocomplete/selection
        $restaurants = Restaurant::orderBy('name')->get();

        return view('reviews.create', compact('network', 'restaurants'));
    }

    /**
     * Store a newly created review in storage
     */
    public function store(Request $request, Network $network)
    {
        // Verify user is member of this network
        if (!$network->members->contains(Auth::id())) {
            abort(403, 'No tienes acceso a esta red.');
        }

        // Validate based on whether creating new restaurant or using existing
        $rules = [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string',
            'date_of_visit' => 'nullable|date',
            'meal_type' => 'nullable|in:breakfast,lunch,dinner',
            // Photo gallery
            'photos' => 'nullable|array|max:10',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max per photo
            // Price and ticket (at least one required)
            'price_amount' => 'nullable|numeric|min:0|max:9999.99',
            'ticket_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,pdf|max:5120',
            'price_notes' => 'nullable|string|max:500',
        ];

        if ($request->input('restaurant_option') === 'new') {
            // Creating new restaurant
            $rules['restaurant_name'] = 'required|string|max:255';
            $rules['restaurant_address'] = 'required|string|max:500';
            $rules['restaurant_cuisine_type'] = 'required|string|max:100';
            $rules['restaurant_city'] = 'nullable|string|max:100';
            $rules['restaurant_latitude'] = 'nullable|numeric|between:-90,90';
            $rules['restaurant_longitude'] = 'nullable|numeric|between:-180,180';
        } else {
            // Using existing restaurant
            $rules['restaurant_id'] = 'required|exists:restaurants,id';
        }

        $validated = $request->validate($rules);

        // Validate that at least price OR ticket photo is provided
        if (!$request->filled('price_amount') && !$request->hasFile('ticket_photo')) {
            return back()
                ->withInput()
                ->withErrors(['price_amount' => 'Debes proporcionar al menos el precio o una foto del ticket.']);
        }

        // Handle restaurant creation if needed
        if ($request->input('restaurant_option') === 'new') {
            $restaurant = Restaurant::create([
                'name' => $validated['restaurant_name'],
                'address' => $validated['restaurant_address'],
                'cuisine_type' => $validated['restaurant_cuisine_type'],
                'city' => $validated['restaurant_city'] ?? null,
                'latitude' => $validated['restaurant_latitude'] ?? null,
                'longitude' => $validated['restaurant_longitude'] ?? null,
            ]);
            $restaurantId = $restaurant->id;
        } else {
            $restaurantId = $validated['restaurant_id'];
        }

        // Create review
        $review = Review::create([
            'network_id' => $network->id,
            'user_id' => Auth::id(),
            'restaurant_id' => $restaurantId,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'date_of_visit' => $validated['date_of_visit'] ?? null,
            'meal_type' => $validated['meal_type'] ?? null,
        ]);

        // Handle photo gallery upload
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $index => $photo) {
                $path = $photo->store('review-photos', 'public');
                $review->photos()->create([
                    'photo_url' => Storage::url($path),
                    'order' => $index,
                ]);
            }
        }

        // Handle ticket photo upload and OCR
        $ticketPhotoUrl = null;
        $ocrDetectedPrice = null;

        if ($request->hasFile('ticket_photo')) {
            $path = $request->file('ticket_photo')->store('ticket-photos', 'public');
            $ticketPhotoUrl = Storage::url($path);

            // Basic OCR simulation - In production, use Tesseract or Cloud Vision API
            // For now, we'll use the manually entered price
            $ocrDetectedPrice = $validated['price_amount'] ?? null;
        }

        // Create price record
        if ($validated['price_amount'] || $ticketPhotoUrl) {
            $review->prices()->create([
                'amount' => $validated['price_amount'] ?? $ocrDetectedPrice ?? 0,
                'currency' => 'EUR',
                'ticket_photo_url' => $ticketPhotoUrl,
                'notes' => $validated['price_notes'] ?? null,
            ]);
        }

        return redirect()
            ->route('networks.reviews.show', [$network, $review])
            ->with('success', '¡Reseña publicada exitosamente!');
    }

    /**
     * Display the specified review
     */
    public function show(Network $network, Review $review)
    {
        // Verify user is member of this network
        if (!$network->members->contains(Auth::id())) {
            abort(403, 'No tienes acceso a esta red.');
        }

        // Verify review belongs to this network
        if ($review->network_id !== $network->id) {
            abort(404);
        }

        $review->load(['user', 'restaurant', 'comments.user']);

        return view('reviews.show', compact('network', 'review'));
    }

    /**
     * Show the form for editing the specified review
     */
    public function edit(Network $network, Review $review)
    {
        // Verify user is member of this network
        if (!$network->members->contains(Auth::id())) {
            abort(403, 'No tienes acceso a esta red.');
        }

        // Verify user owns this review
        if ($review->user_id !== Auth::id()) {
            abort(403, 'No puedes editar esta reseña.');
        }

        // Verify review belongs to this network
        if ($review->network_id !== $network->id) {
            abort(404);
        }

        $restaurants = Restaurant::orderBy('name')->get();

        return view('reviews.edit', compact('network', 'review', 'restaurants'));
    }

    /**
     * Update the specified review in storage
     */
    public function update(Request $request, Network $network, Review $review)
    {
        // Verify user owns this review
        if ($review->user_id !== Auth::id()) {
            abort(403, 'No puedes editar esta reseña.');
        }

        // Verify review belongs to this network
        if ($review->network_id !== $network->id) {
            abort(404);
        }

        $validated = $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string',
            'date_of_visit' => 'nullable|date',
            'meal_type' => 'nullable|in:breakfast,lunch,dinner',
        ]);

        $review->update($validated);

        return redirect()
            ->route('networks.reviews.show', [$network, $review])
            ->with('success', 'Reseña actualizada exitosamente.');
    }

    /**
     * Remove the specified review from storage
     */
    public function destroy(Network $network, Review $review)
    {
        // Verify user owns this review
        if ($review->user_id !== Auth::id()) {
            abort(403, 'No puedes eliminar esta reseña.');
        }

        // Verify review belongs to this network
        if ($review->network_id !== $network->id) {
            abort(404);
        }

        $review->delete();

        return redirect()
            ->route('networks.reviews.index', $network)
            ->with('success', 'Reseña eliminada exitosamente.');
    }
}
