<?php

use App\Models\Booking;
use App\Models\Category;
use App\Models\ItemRequest;
use App\Models\Listing;
use App\Models\Offer;
use App\Models\User;

test('full hire loop: post, offer, accept, chat, payment, complete, review', function () {
    $renter = User::factory()->create(['name' => 'Loop Renter']);
    $lender = User::factory()->create(['name' => 'Loop Lender']);
    $category = Category::factory()->create();

    $this->actingAs($renter)->post('/requests', [
        'category_id' => $category->id,
        'title' => 'Need a pressure washer',
        'description' => 'For a driveway clean.',
        'location' => 'Harare',
        'needed_from' => now()->addDays(2)->toDateString(),
        'needed_to' => now()->addDays(3)->toDateString(),
    ])->assertRedirect();

    $itemRequest = ItemRequest::firstOrFail();
    expect($itemRequest->title)->toBe('Need a pressure washer');

    $this->actingAs($lender)->post('/offers', [
        'item_request_id' => $itemRequest->id,
        'price' => 15,
    ])->assertRedirect();

    $offer = Offer::firstOrFail();
    expect($offer->status)->toBe('PENDING');

    $this->actingAs($renter)->post("/offers/{$offer->id}/accept")->assertRedirect();

    $booking = Booking::firstOrFail();
    expect($booking->status)->toBe('CONFIRMED');
    expect($offer->fresh()->status)->toBe('ACCEPTED');
    expect($itemRequest->fresh()->status)->toBe('MATCHED');

    $this->actingAs($renter)->post("/bookings/{$booking->id}/messages", ['body' => 'Can you drop it off Saturday?'])->assertRedirect();
    $this->actingAs($lender)->post("/bookings/{$booking->id}/messages", ['body' => 'Yes, 9am works.'])->assertRedirect();
    expect($booking->messages()->count())->toBe(2);

    $this->actingAs($lender)->post("/bookings/{$booking->id}/payment-method", ['payment_method' => 'ECOCASH'])->assertRedirect();
    $this->actingAs($lender)->post("/bookings/{$booking->id}/mark-paid")->assertRedirect();
    expect($booking->fresh()->paid_at)->not->toBeNull();

    $this->actingAs($lender)->post("/bookings/{$booking->id}/complete")->assertRedirect();
    expect($booking->fresh()->status)->toBe('COMPLETED');

    $this->actingAs($lender)->post("/bookings/{$booking->id}/review", [
        'rating' => 5,
        'comment' => 'Great renter.',
    ])->assertRedirect();

    $review = $booking->reviews()->firstOrFail();
    expect($review->target_id)->toBe($renter->id);
    expect($review->rating)->toBe(5);

    $renterProfile = $this->actingAs($renter)->get('/profile');
    $renterProfile->assertSee('5★');
    $renterProfile->assertSee('Great renter.');
});

test('accepting an offer blocks a double booking for the same listing on overlapping dates', function () {
    $lender = User::factory()->create();
    $renterOne = User::factory()->create();
    $renterTwo = User::factory()->create();
    $category = Category::factory()->create();

    $listing = Listing::factory()->create(['owner_id' => $lender->id, 'category_id' => $category->id]);

    $requestOne = ItemRequest::factory()->create([
        'requester_id' => $renterOne->id,
        'category_id' => $category->id,
        'needed_from' => now()->addDays(5),
        'needed_to' => now()->addDays(7),
    ]);
    $requestTwo = ItemRequest::factory()->create([
        'requester_id' => $renterTwo->id,
        'category_id' => $category->id,
        'needed_from' => now()->addDays(6),
        'needed_to' => now()->addDays(8),
    ]);

    $offerOne = Offer::factory()->create([
        'item_request_id' => $requestOne->id,
        'listing_id' => $listing->id,
        'lender_id' => $lender->id,
    ]);
    $offerTwo = Offer::factory()->create([
        'item_request_id' => $requestTwo->id,
        'listing_id' => $listing->id,
        'lender_id' => $lender->id,
    ]);

    $this->actingAs($renterOne)->post("/offers/{$offerOne->id}/accept")->assertRedirect();
    expect(Booking::count())->toBe(1);

    $response = $this->actingAs($renterTwo)->post("/offers/{$offerTwo->id}/accept");

    $response->assertSessionHasErrors('offer');
    expect(Booking::count())->toBe(1);
});

test('listing photo upload shows on the detail page and browse card', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['name' => 'Cameras', 'slug' => 'cameras']);

    $fixture = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=';

    $response = $this->actingAs($user)->post('/listings', [
        'category_id' => $category->id,
        'title' => 'Canon DSLR camera',
        'description' => 'Great camera for hire.',
        'price' => 20,
        'price_unit' => 'day',
        'location' => 'Harare',
        'photos_json' => json_encode([$fixture]),
    ]);

    $response->assertRedirect();
    $listing = Listing::firstOrFail();
    expect($listing->photos)->toBe([$fixture]);

    $this->get(route('listings.show', $listing))->assertSee($fixture, false);
    $this->get('/browse')->assertSee($fixture, false);
});
