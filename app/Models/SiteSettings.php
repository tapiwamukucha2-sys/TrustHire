<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSettings extends Model
{
    protected $table = 'site_settings';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['id', 'hero_headline', 'hero_subtext', 'announcement_text', 'announcement_enabled'];

    protected function casts(): array
    {
        return [
            'announcement_enabled' => 'boolean',
        ];
    }

    public static function current(): self
    {
        return static::firstOrCreate(
            ['id' => 'singleton'],
            [
                'hero_headline' => 'Hire or Rent Anything, Anywhere, Anytime.',
                'hero_subtext' => 'Access tools, cameras, luxury vehicles, homes, fashion, computers and many more from verified owners in your community. Save money, reduce waste.',
            ]
        );
    }
}
