<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create {email?} {password?} {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin user for production';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email') ?? 'admin@dba.com';
        $password = $this->argument('password') ?? 'password';
        $name = $this->argument('name') ?? 'Administrador';

        // Verificar si el usuario ya existe
        if (User::where('email', $email)->exists()) {
            $this->info("Usuario {$email} ya existe.");

            return self::SUCCESS;
        }

        // Crear usuario
        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);

        $this->info('✅ Usuario creado exitosamente:');
        $this->line("   Email: {$email}");
        $this->line("   Password: {$password}");

        return self::SUCCESS;
    }
}
