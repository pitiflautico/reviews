<?php

namespace App\Http\Controllers;

use App\Models\Network;
use App\Models\Review;
use App\Models\ReviewComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewCommentController extends Controller
{
    /**
     * Store a newly created comment in storage
     */
    public function store(Request $request, Network $network, Review $review)
    {
        // Verify user is member of this network
        if (!$network->members->contains(Auth::id())) {
            abort(403, 'No tienes acceso a esta red.');
        }

        // Verify review belongs to this network
        if ($review->network_id !== $network->id) {
            abort(404);
        }

        $validated = $request->validate([
            'comment' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:review_comments,id',
        ]);

        $validated['review_id'] = $review->id;
        $validated['user_id'] = Auth::id();

        // If parent_id is provided, verify it belongs to this review
        if (isset($validated['parent_id'])) {
            $parentComment = ReviewComment::find($validated['parent_id']);
            if ($parentComment->review_id !== $review->id) {
                abort(400, 'Comentario padre inválido.');
            }
        }

        ReviewComment::create($validated);

        return redirect()
            ->route('networks.reviews.show', [$network, $review])
            ->with('success', 'Comentario añadido exitosamente.');
    }
}
