<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class RelinkCreateImporterUser extends Command
{
    protected $signature = 'relink:create-importer {--email=} {--name=}';
    protected $description = 'Create (or ensure) a dedicated importer user for Relink ingestion.';

    public function handle(): int
    {
        $email = $this->option('email') ?: config('ingestion.relink.importer_email');
        $name = $this->option('name') ?: 'reLink Importer';

        if (!$email) {
            $this->error('No email provided. Set RELINK_IMPORTER_EMAIL in .env or pass --email=');
            return self::FAILURE;
        }

        $user = User::where('email', $email)->first();

        if ($user) {
            $this->info("Importer user already exists: {$user->email} (id={$user->id})");
            return self::SUCCESS;
        }

        // random password, user never logs in
        $password = bin2hex(random_bytes(16));

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $this->info("Created importer user: {$user->email} (id={$user->id})");
        $this->warn("Generated password (store if needed): {$password}");

        return self::SUCCESS;
    }
}
