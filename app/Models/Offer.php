<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = ['item_request_id', 'listing_id', 'lender_id', 'price', 'message', 'status'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function itemRequest(): BelongsTo
    {
        return $this->belongsTo(ItemRequest::class);
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function lender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lender_id');
    }

    public function booking(): HasOne
    {
        return $this->hasOne(Booking::class);
    }
}
