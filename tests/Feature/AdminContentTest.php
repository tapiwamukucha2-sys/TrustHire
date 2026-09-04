<?php

use App\Models\Category;
use App\Models\User;

test('admin can edit site content and it reflects on the homepage', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post('/admin/content', [
        'hero_headline' => 'Test Headline 123',
        'hero_subtext' => 'Test subtext.',
        'announcement_enabled' => '1',
        'announcement_text' => 'Test announcement banner',
    ]);

    $response->assertRedirect();

    $home = $this->get('/');
    $home->assertSee('Test Headline 123');
    $home->assertSee('Test announcement banner');
});

test('admin can add, rename, and delete a category', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->post('/admin/categories', ['name' => 'Test Category'])->assertRedirect();
    $category = Category::where('name', 'Test Category')->firstOrFail();

    $this->actingAs($admin)->post("/admin/categories/{$category->id}", ['name' => 'Renamed Category'])->assertRedirect();
    expect($category->fresh()->name)->toBe('Renamed Category');

    $this->actingAs($admin)->post("/admin/categories/{$category->id}/delete")->assertRedirect();
    expect(Category::find($category->id))->toBeNull();
});

test('a category with listings cannot be deleted', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::factory()->create();
    \App\Models\Listing::factory()->create(['category_id' => $category->id]);

    $response = $this->actingAs($admin)->post("/admin/categories/{$category->id}/delete");

    $response->assertSessionHasErrors('category');
    expect(Category::find($category->id))->not->toBeNull();
});

test('a non-admin cannot access the admin content panel', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/admin/content')->assertRedirect('/admin/login');
});
