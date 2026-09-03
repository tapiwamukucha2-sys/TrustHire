<?php

use App\Models\Category;
use App\Models\Listing;
use App\Models\User;

test('the homepage loads and shows categories', function () {
    Category::factory()->create(['name' => 'Cameras', 'slug' => 'cameras']);

    $response = $this->get('/');

    $response->assertOk();
    $response->assertSee('Cameras');
});

test('the homepage title tag is not clobbered by loop variables', function () {
    $response = $this->get('/');

    $response->assertOk();
    $response->assertSee('<title>TrustHire — hire local, hire verified</title>', false);
});

test('the homepage shows nothing under Featured when there are no active listings', function () {
    $response = $this->get('/');

    $response->assertOk();
    $response->assertDontSee('Featured on TrustHire');
});

test('the homepage shows real active listings grouped by category', function () {
    $owner = User::factory()->create();
    $cameras = Category::factory()->create(['name' => 'Cameras', 'slug' => 'cameras']);

    Listing::factory()->create([
        'owner_id' => $owner->id,
        'category_id' => $cameras->id,
        'title' => 'Canon DSLR',
        'status' => 'ACTIVE',
    ]);
    Listing::factory()->create([
        'owner_id' => $owner->id,
        'category_id' => $cameras->id,
        'title' => 'Draft Camera',
        'status' => 'PAUSED',
    ]);

    $response = $this->get('/');

    $response->assertOk();
    $response->assertSee('Featured on TrustHire');
    $response->assertSee('Canon DSLR');
    $response->assertDontSee('Draft Camera');
});

test('browse shows active listings filtered by category', function () {
    $owner = User::factory()->create();
    $cameras = Category::factory()->create(['name' => 'Cameras', 'slug' => 'cameras']);
    $tools = Category::factory()->create(['name' => 'Tools', 'slug' => 'tools']);

    Listing::factory()->create(['owner_id' => $owner->id, 'category_id' => $cameras->id, 'title' => 'Canon DSLR', 'status' => 'ACTIVE']);
    Listing::factory()->create(['owner_id' => $owner->id, 'category_id' => $tools->id, 'title' => 'Power Drill', 'status' => 'ACTIVE']);

    $response = $this->get('/browse?category=cameras');

    $response->assertOk();
    $response->assertSee('Canon DSLR');
    $response->assertDontSee('Power Drill');
});
