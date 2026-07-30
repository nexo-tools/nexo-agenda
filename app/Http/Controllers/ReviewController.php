<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function store(Request $request, string $token): RedirectResponse
    {
        $booking = Booking::findByManagementToken($token);

        abort_if($booking === null, 404);

        if (! $booking->canBeReviewed()) {
            return redirect()->route('booking.manage', $token);
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:500'],
        ]);

        $booking->business->reviews()->create([
            'booking_id' => $booking->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'client_name' => $booking->client_name,
        ]);

        return redirect()->route('booking.manage', $token)->with('status', __('Thanks for your review!'));
    }

    public function index(Request $request): View
    {
        return view('app.reviews', [
            'reviews' => $request->user()->business->reviews()->latest()->with('booking.service')->get(),
        ]);
    }

    public function toggle(Review $review): RedirectResponse
    {
        Gate::authorize('moderate', $review);

        $review->update(['is_hidden' => ! $review->is_hidden]);

        return back()->with('status', $review->is_hidden ? __('Review hidden.') : __('Review visible.'));
    }
}
