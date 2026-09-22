<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name', 'email', 'username', 'phone', 'password', 'google_id', 'account_type', 'location', 'photo_url',
    'marketing_opt_in', 'terms_accepted_at', 'profile_completed_at', 'is_admin',
    'verification_tier', 'id_document_url', 'selfie_url',
    'verification_requested_at', 'verification_reviewed_at', 'verification_reject_reason',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'marketing_opt_in' => 'boolean',
            'is_admin' => 'boolean',
            'terms_accepted_at' => 'datetime',
            'profile_completed_at' => 'datetime',
            'verification_requested_at' => 'datetime',
            'verification_reviewed_at' => 'datetime',
        ];
    }

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class, 'owner_id');
    }

    public function itemRequests(): HasMany
    {
        return $this->hasMany(ItemRequest::class, 'requester_id');
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class, 'lender_id');
    }

    public function bookingsAsLender(): HasMany
    {
        return $this->hasMany(Booking::class, 'lender_id');
    }

    public function bookingsAsRenter(): HasMany
    {
        return $this->hasMany(Booking::class, 'renter_id');
    }

    public function reviewsWritten(): HasMany
    {
        return $this->hasMany(Review::class, 'author_id');
    }

    public function reviewsReceived(): HasMany
    {
        return $this->hasMany(Review::class, 'target_id');
    }

    public function reportsFiled(): HasMany
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    public function displayName(): string
    {
        return $this->name ?? $this->phone ?? $this->email ?? 'Someone';
    }
}
