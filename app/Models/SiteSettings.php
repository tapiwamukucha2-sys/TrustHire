<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SiteSettings extends Model
{
    protected $table = 'site_settings';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['id', 'hero_headline', 'hero_subtext', 'announcement_text', 'announcement_enabled', 'hero_image_1', 'hero_image_2', 'trust_image_1', 'trust_image_2'];

    protected function casts(): array
    {
        return [
            'announcement_enabled' => 'boolean',
        ];
    }

    public function heroImage1Url(): string
    {
        return $this->hero_image_1 ? Storage::disk(config('filesystems.uploads'))->url($this->hero_image_1) : '/storage/demo/hero-1.jpg';
    }

    public function heroImage2Url(): string
    {
        return $this->hero_image_2 ? Storage::disk(config('filesystems.uploads'))->url($this->hero_image_2) : '/storage/demo/hero-2.jpg';
    }

    public function trustImage1Url(): string
    {
        return $this->trust_image_1 ? Storage::disk(config('filesystems.uploads'))->url($this->trust_image_1) : '/storage/demo/trust-handshake.jpg';
    }

    public function trustImage2Url(): string
    {
        return $this->trust_image_2 ? Storage::disk(config('filesystems.uploads'))->url($this->trust_image_2) : '/storage/demo/trust-keyhandover.jpg';
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
