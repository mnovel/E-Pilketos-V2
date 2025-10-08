<?php

namespace Database\Seeders;

use App\Models\Candidates;
use App\Models\ElectionSessions;
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
        User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $session = ElectionSessions::create([
            'name' => 'Session 1',
            'start_date' => now(),
            'end_date' => now()->addDays(7),
        ]);

        $class = $session->classes()->create([
            'name' => '12 Mipa 1',
            'max_users' => 30,
        ]);

        for ($i = 1; $i <= 30; $i++) {
            $user = User::create([
                'name' => "Peserta {$i}",
                'email' => "peserta{$i}@example.com",
                'password' => bcrypt('password'),
                'role' => 'voter',
            ]);

            $user->participant()->create([
                'nisn' => str_pad($i, 10, '0', STR_PAD_LEFT),
                'voting_status' => null,
                'class_id' => $class->id,
            ]);
        }

        Candidates::factory(3)->create();

        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => 'admin123',
            'role' => 'admin',
        ]);

        $user = User::create([
            'name' => 'Petugas',
            'email' => 'petugas@gmail.com',
            'password' => 'petugas123',
            'role' => 'voter management',
        ]);
    }
}
