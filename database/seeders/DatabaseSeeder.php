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

        $user = User::create([
            'name' => 'Peserta 1',
            'email' => 'peserta1@example.com',
            'password' => 'password',
            'role' => 'voter',
        ]);

        $user->participant()->create([
            'nisn' => '1234567890',
            'voting_status' => null,
            'class_id' => $class->id,
        ]);

        Candidates::factory(3)->create();
    }
}
