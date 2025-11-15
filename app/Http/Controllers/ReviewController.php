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

        $validated = $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string',
            'date_of_visit' => 'nullable|date',
            'meal_type' => 'nullable|in:breakfast,lunch,dinner',
        ]);

        $validated['network_id'] = $network->id;
        $validated['user_id'] = Auth::id();

        $review = Review::create($validated);

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
