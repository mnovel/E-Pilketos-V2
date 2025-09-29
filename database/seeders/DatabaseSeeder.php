<?php

namespace Database\Seeders;

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
        // User::factory(10)->create();

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
    }
}
