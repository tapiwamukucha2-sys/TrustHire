<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    private const PAYMENT_METHODS = ['ECOCASH', 'ONEMONEY', 'TELECASH', 'PAYNOW', 'BANK_TRANSFER', 'CASH', 'OTHER'];

    private function authorizeParticipant(Request $request, Booking $booking): void
    {
        if ($booking->lender_id !== $request->user()->id && $booking->renter_id !== $request->user()->id) {
            abort(403, 'You are not part of this booking.');
        }
    }

    public function show(Request $request, Booking $booking): View
    {
        $this->authorizeParticipant($request, $booking);
        $booking->load('itemRequest.category', 'offer', 'lender', 'renter', 'messages.sender', 'reviews');

        return view('bookings.show', ['booking' => $booking]);
    }

    public function sendMessage(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorizeParticipant($request, $booking);

        $data = $request->validate(['body' => 'required|string']);

        $booking->messages()->create([
            'sender_id' => $request->user()->id,
            'body' => trim($data['body']),
        ]);

        return back();
    }

    public function setPaymentMethod(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorizeParticipant($request, $booking);

        $data = $request->validate(['payment_method' => 'required|in:'.implode(',', self::PAYMENT_METHODS)]);

        $booking->update(['payment_method' => $data['payment_method']]);

        return back();
    }

    public function markPaid(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorizeParticipant($request, $booking);

        if (! $booking->payment_method) {
            return back()->withErrors(['payment' => 'Select a payment method first.']);
        }

        $booking->update(['paid_at' => now()]);

        return back();
    }

    public function complete(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorizeParticipant($request, $booking);

        $booking->update(['status' => 'COMPLETED']);

        return back();
    }

    public function submitReview(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorizeParticipant($request, $booking);

        if ($booking->status !== 'COMPLETED') {
            return back()->withErrors(['review' => 'You can only review a completed booking.']);
        }

        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $targetId = $booking->lender_id === $request->user()->id ? $booking->renter_id : $booking->lender_id;

        $booking->reviews()->updateOrCreate(
            ['author_id' => $request->user()->id],
            ['target_id' => $targetId, 'rating' => $data['rating'], 'comment' => $data['comment'] ?? null]
        );

        return back();
    }
}
