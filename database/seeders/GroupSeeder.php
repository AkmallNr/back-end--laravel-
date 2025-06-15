<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Group;
use App\Models\User;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan ada data di tabel users
        $users = User::all();
        if ($users->isEmpty()) {
            $this->command->info('No users found. Please seed the users table first.');
            return;
        }

        // Data dummy untuk groups
        $groupsData = [
            [
                'name' => 'Tanpa Label',
                'icon' => 'fas fa-false',
                'userId' => $users->id,
            ]
        ];

        // Masukkan data menggunakan model Group
        foreach ($groupsData as $data) {
            Group::create($data);
        }

        $this->command->info('Groups seeded successfully!');
    }
}
