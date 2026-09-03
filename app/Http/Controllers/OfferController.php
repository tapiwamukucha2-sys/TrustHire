<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Offer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OfferController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'item_request_id' => 'required|exists:item_requests,id',
            'listing_id' => 'nullable|exists:listings,id',
            'price' => 'required|numeric|min:0',
            'message' => 'nullable|string',
        ]);

        Offer::create([
            'item_request_id' => $data['item_request_id'],
            'listing_id' => $data['listing_id'] ?? null,
            'lender_id' => $request->user()->id,
            'price' => $data['price'],
            'message' => $data['message'] ?? null,
        ]);

        return back();
    }

    public function accept(Request $request, Offer $offer): RedirectResponse
    {
        $offer->load('itemRequest');

        if ($offer->itemRequest->requester_id !== $request->user()->id) {
            abort(403, 'Only the requester can accept an offer.');
        }
        if ($offer->itemRequest->status !== 'OPEN') {
            return back()->withErrors(['offer' => 'This request is no longer open.']);
        }

        try {
            $booking = DB::transaction(function () use ($offer, $request) {
                if ($offer->listing_id) {
                    $conflict = Booking::query()
                        ->whereHas('offer', fn ($q) => $q->where('listing_id', $offer->listing_id))
                        ->whereIn('status', ['CONFIRMED', 'COMPLETED'])
                        ->where('start_date', '<', $offer->itemRequest->needed_to)
                        ->where('end_date', '>', $offer->itemRequest->needed_from)
                        ->exists();

                    if ($conflict) {
                        throw new \RuntimeException('This item is already booked for overlapping dates. Ask the lender for different dates.');
                    }
                }

                $offer->update(['status' => 'ACCEPTED']);
                Offer::where('item_request_id', $offer->item_request_id)
                    ->where('id', '!=', $offer->id)
                    ->where('status', 'PENDING')
                    ->update(['status' => 'DECLINED']);
                $offer->itemRequest->update(['status' => 'MATCHED']);

                return Booking::create([
                    'item_request_id' => $offer->item_request_id,
                    'offer_id' => $offer->id,
                    'lender_id' => $offer->lender_id,
                    'renter_id' => $request->user()->id,
                    'start_date' => $offer->itemRequest->needed_from,
                    'end_date' => $offer->itemRequest->needed_to,
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors(['offer' => $e->getMessage()]);
        }

        return redirect()->route('bookings.show', $booking);
    }
}
