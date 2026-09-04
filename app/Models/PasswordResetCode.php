<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetCode extends Model
{
    protected $fillable = ['identifier', 'code', 'expires_at', 'consumed_at'];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'consumed_at' => 'datetime',
        ];
    }

    public static function generateFor(string $identifier): string
    {
        $code = (string) random_int(100000, 999999);

        static::create([
            'identifier' => $identifier,
            'code' => $code,
            'expires_at' => now()->addMinutes(15),
        ]);

        return $code;
    }

    public static function verify(string $identifier, string $code): bool
    {
        $reset = static::where('identifier', $identifier)
            ->where('code', $code)
            ->whereNull('consumed_at')
            ->where('expires_at', '>', now())
            ->latest('id')
            ->first();

        if (! $reset) {
            return false;
        }

        $reset->update(['consumed_at' => now()]);

        return true;
    }
}
