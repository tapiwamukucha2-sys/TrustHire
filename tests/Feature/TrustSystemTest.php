<?php

use App\Models\Category;
use App\Models\Listing;
use App\Models\Report;
use App\Models\User;

test('verification: submit, admin approve, badge appears; report: file and resolve', function () {
    $user = User::factory()->create(['name' => 'Trust Admin']);
    $fixture = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=';

    $this->actingAs($user)->post('/verify', [
        'id_document_url' => $fixture,
        'selfie_url' => $fixture,
    ])->assertRedirect();

    expect($user->fresh()->verification_tier)->toBe('PENDING');

    // Not an admin yet.
    $this->actingAs($user)->get('/admin/verifications')->assertRedirect('/');

    $user->update(['is_admin' => true]);

    $this->actingAs($user)->get('/admin/verifications')->assertSee('Trust Admin');
    $this->actingAs($user)->post("/admin/verifications/{$user->id}/approve")->assertRedirect();

    expect($user->fresh()->verification_tier)->toBe('VERIFIED');
    $this->actingAs($user)->get('/profile')->assertSee('Verified');

    // Post a listing to report.
    $category = Category::factory()->create();
    $this->actingAs($user)->post('/listings', [
        'category_id' => $category->id,
        'title' => 'Reportable item',
        'description' => 'desc',
        'price' => 5,
        'price_unit' => 'day',
        'location' => 'Harare',
    ])->assertRedirect();
    $listing = Listing::firstOrFail();

    $reporter = User::factory()->create();
    $this->actingAs($reporter)->post('/reports', [
        'target_listing_id' => $listing->id,
        'reason' => 'SCAM',
        'details' => 'Looks suspicious.',
    ])->assertRedirect();

    $report = Report::firstOrFail();
    expect($report->status)->toBe('OPEN');

    $this->actingAs($user)->get('/admin/reports')->assertSee('Looks suspicious.');
    $this->actingAs($user)->post("/admin/reports/{$report->id}/resolve", ['status' => 'RESOLVED'])->assertRedirect();

    expect($report->fresh()->status)->toBe('RESOLVED');
    $this->actingAs($user)->get('/admin/reports')->assertDontSee('Looks suspicious.');
});

test('a non-admin cannot approve verifications', function () {
    $admin = User::factory()->create();
    $regular = User::factory()->create();

    $response = $this->actingAs($regular)->post("/admin/verifications/{$admin->id}/approve");

    $response->assertRedirect('/');
    expect($admin->fresh()->verification_tier)->toBe('BASIC');
});
