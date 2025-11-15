<?php

namespace App\Http\Controllers;

use App\Models\Network;
use App\Models\Restaurant;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Laravel\Facades\Image;
use thiagoalessio\TesseractOCR\TesseractOCR;

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

        // Handle photo gallery upload with compression
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $index => $photo) {
                $compressedPath = $this->compressImage($photo, 'review-photos');
                $review->photos()->create([
                    'photo_url' => $compressedPath,
                    'order' => $index,
                ]);
            }
        }

        // Handle ticket photo upload and OCR
        $ticketPhotoUrl = null;
        $ocrDetectedPrice = null;

        if ($request->hasFile('ticket_photo')) {
            $ticketFile = $request->file('ticket_photo');

            // Save ticket photo with compression
            $ticketPhotoUrl = $this->compressImage($ticketFile, 'ticket-photos');

            // Perform OCR to extract price
            try {
                $ocrDetectedPrice = $this->extractPriceFromTicket($ticketFile);
            } catch (\Exception $e) {
                Log::warning('OCR failed: ' . $e->getMessage());
            }
        }

        // Create price record
        if ($validated['price_amount'] || $ticketPhotoUrl) {
            // Use manually entered price if available, otherwise use OCR detected price
            $finalPrice = $validated['price_amount'] ?? $ocrDetectedPrice ?? 0;

            $review->prices()->create([
                'amount' => $finalPrice,
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

    /**
     * Compress and optimize image to ~100KB
     * Max 1MB allowed
     */
    private function compressImage($file, $directory): string
    {
        // Read image with Intervention
        $image = Image::read($file);

        // Resize maintaining aspect ratio (max width: 1200px)
        $image->scaleDown(width: 1200);

        // Generate unique filename
        $filename = uniqid() . '.jpg';
        $path = $directory . '/' . $filename;
        $fullPath = storage_path('app/public/' . $path);

        // Ensure directory exists
        if (!file_exists(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        // Start with quality 85
        $quality = 85;
        $image->toJpeg($quality)->save($fullPath);

        // Check file size and reduce quality if needed to reach ~100KB
        $fileSize = filesize($fullPath);
        $targetSize = 100 * 1024; // 100KB target
        $maxSize = 1024 * 1024; // 1MB max

        // Iteratively reduce quality to reach target size
        while ($fileSize > $targetSize && $quality > 40) {
            $quality -= 5;
            $image->toJpeg($quality)->save($fullPath);
            $fileSize = filesize($fullPath);
        }

        // If still over 1MB, resize further and compress more
        if ($fileSize > $maxSize) {
            $image->scaleDown(width: 800);
            $quality = 60;
            $image->toJpeg($quality)->save($fullPath);
        }

        return Storage::url($path);
    }

    /**
     * Extract price from ticket using Tesseract OCR
     */
    private function extractPriceFromTicket($file): ?float
    {
        // Save temp file for OCR processing
        $tempPath = $file->store('temp');
        $fullPath = storage_path('app/' . $tempPath);

        try {
            // Run Tesseract OCR
            $ocr = new TesseractOCR($fullPath);
            $ocr->lang('spa', 'eng'); // Spanish and English
            $text = $ocr->run();

            // Clean up temp file
            Storage::delete($tempPath);

            // Extract price patterns (common in Spanish/European tickets)
            $patterns = [
                '/TOTAL[:\s]*(\d+[.,]\d{2})/',      // TOTAL: 12.50
                '/IMPORTE[:\s]*(\d+[.,]\d{2})/',    // IMPORTE: 12.50
                '/(\d+[.,]\d{2})\s*€/',              // 12.50€ or 12,50€
                '/€\s*(\d+[.,]\d{2})/',              // €12.50
                '/(\d{1,3}[.,]\d{2})/',              // 12.50 or 12,50 (last resort)
            ];

            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $text, $matches)) {
                    // Convert comma to dot for decimal
                    $price = str_replace(',', '.', $matches[1]);
                    $priceFloat = (float) $price;

                    // Validate reasonable price range (0.01 to 999.99)
                    if ($priceFloat >= 0.01 && $priceFloat <= 999.99) {
                        return $priceFloat;
                    }
                }
            }

            return null;
        } catch (\Exception $e) {
            // Clean up temp file on error
            if (Storage::exists($tempPath)) {
                Storage::delete($tempPath);
            }
            throw $e;
        }
    }
}
