<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeUserAdmin extends Command
{
    protected $signature = 'user:make-admin {identifier : Phone or email of the user}';

    protected $description = 'Promote a user to admin by phone or email';

    public function handle(): int
    {
        $identifier = $this->argument('identifier');

        $user = User::where('phone', $identifier)->orWhere('email', $identifier)->first();

        if (! $user) {
            $this->error("No user found with phone/email \"{$identifier}\". They must log in at least once first.");

            return self::FAILURE;
        }

        $user->update(['is_admin' => true]);
        $this->info(($user->name ?? $identifier).' is now an admin.');

        return self::SUCCESS;
    }
}
