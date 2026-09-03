<?php

use App\Models\Booking;
use App\Models\Category;
use App\Models\ItemRequest;
use App\Models\Listing;
use App\Models\Offer;
use App\Models\User;

test('browse filters listings by price range', function () {
    $owner = User::factory()->create();
    $category = Category::factory()->create();

    Listing::factory()->create(['owner_id' => $owner->id, 'category_id' => $category->id, 'title' => 'Cheap Item', 'price' => 5]);
    Listing::factory()->create(['owner_id' => $owner->id, 'category_id' => $category->id, 'title' => 'Mid Item', 'price' => 50]);
    Listing::factory()->create(['owner_id' => $owner->id, 'category_id' => $category->id, 'title' => 'Pricey Item', 'price' => 500]);

    $response = $this->get('/browse?price_min=10&price_max=100');

    $response->assertOk();
    $response->assertDontSee('Cheap Item');
    $response->assertSee('Mid Item');
    $response->assertDontSee('Pricey Item');
});

test('browse can filter to verified owners only', function () {
    $verifiedOwner = User::factory()->verified()->create();
    $basicOwner = User::factory()->create();
    $category = Category::factory()->create();

    Listing::factory()->create(['owner_id' => $verifiedOwner->id, 'category_id' => $category->id, 'title' => 'Verified Item']);
    Listing::factory()->create(['owner_id' => $basicOwner->id, 'category_id' => $category->id, 'title' => 'Unverified Item']);

    $response = $this->get('/browse?verified_only=1');

    $response->assertOk();
    $response->assertSee('Verified Item');
    $response->assertDontSee('Unverified Item');
});

test('browse date-range filter excludes listings already booked over the requested window', function () {
    $lender = User::factory()->create();
    $renter = User::factory()->create();
    $category = Category::factory()->create();

    $listing = Listing::factory()->create(['owner_id' => $lender->id, 'category_id' => $category->id, 'title' => 'Booked Camera']);
    $freeListing = Listing::factory()->create(['owner_id' => $lender->id, 'category_id' => $category->id, 'title' => 'Free Camera']);

    $itemRequest = ItemRequest::factory()->create([
        'requester_id' => $renter->id,
        'category_id' => $category->id,
        'needed_from' => '2026-10-10',
        'needed_to' => '2026-10-12',
    ]);
    $offer = Offer::factory()->create([
        'item_request_id' => $itemRequest->id,
        'listing_id' => $listing->id,
        'lender_id' => $lender->id,
    ]);
    Booking::factory()->create([
        'item_request_id' => $itemRequest->id,
        'offer_id' => $offer->id,
        'lender_id' => $lender->id,
        'renter_id' => $renter->id,
        'start_date' => '2026-10-10',
        'end_date' => '2026-10-12',
        'status' => 'CONFIRMED',
    ]);

    $response = $this->get('/browse?date_from=2026-10-11&date_to=2026-10-13');

    $response->assertOk();
    $response->assertDontSee('Booked Camera');
    $response->assertSee('Free Camera');

    // Outside the booked window, the same listing should be available again.
    $response = $this->get('/browse?date_from=2026-11-01&date_to=2026-11-03');
    $response->assertSee('Booked Camera');
});

test('browse filters requests by budget and verified requester', function () {
    $verifiedRenter = User::factory()->verified()->create();
    $category = Category::factory()->create();

    ItemRequest::factory()->create(['requester_id' => $verifiedRenter->id, 'category_id' => $category->id, 'title' => 'Verified Ask', 'budget' => 50]);
    ItemRequest::factory()->create(['requester_id' => User::factory(), 'category_id' => $category->id, 'title' => 'Basic Ask', 'budget' => 500]);

    $response = $this->get('/browse?tab=requests&verified_only=1&price_max=100');

    $response->assertOk();
    $response->assertSee('Verified Ask');
    $response->assertDontSee('Basic Ask');
});
