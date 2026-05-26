<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@lexfind.com',
            'password' => bcrypt('Rain@4342a'),
            'system' => 'simp',
        ]);

        // Seed Administrator (ghost role)
        User::create([
            'name' => 'Ghost User',
            'email' => 'ghost@lexfind.com',
            'password' => bcrypt('Rain@4342v'),
            'system' => 'ghost',
        ]);

        User::create([
            'name' => 'Moderator User',
            'email' => 'mod@lexfind.com',
            'password' => bcrypt('Rain@4342b'),
            'system' => 'bat',
        ]);

        User::create([
            'name' => 'Front Mod User',
            'email' => 'fmod@lexfind.com',
            'password' => bcrypt('Rain@4342b'),
            'system' => 'bip',
        ]);

        // Seed Regular User (god/standard user role)
        User::create([
            'name' => 'Regular User',
            'email' => 'user@lexfind.com',
            'password' => bcrypt('Rain@4342'),
            'system' => 'god',
        ]);

        // Seed User for Brian Barakat profile assignment
        User::create([
            'name' => 'Brian Barakat User',
            'email' => 'barakat@lexfind.com',
            'password' => bcrypt('Rain@4342'),
            'system' => 'god',
        ]);

        // Seed User for Giacomo Bossa profile assignment
        User::create([
            'name' => 'Giacomo Bossa User',
            'email' => 'bossa@lexfind.com',
            'password' => bcrypt('Rain@4342'),
            'system' => 'god',
        ]);

        // Call LawyerSeeder
        $this->call(LawyerSeeder::class);
    }
}
