<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ItemRequest extends Model
{
    use HasFactory;

    protected $table = 'item_requests';

    protected $fillable = [
        'requester_id', 'category_id', 'title', 'description', 'budget', 'location', 'needed_from', 'needed_to', 'status',
    ];

    protected function casts(): array
    {
        return [
            'needed_from' => 'date',
            'needed_to' => 'date',
            'budget' => 'decimal:2',
        ];
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    public function booking(): HasOne
    {
        return $this->hasOne(Booking::class);
    }
}
