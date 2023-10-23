<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        $kotyk = User::factory()->create([
            'name' => 'kotyk',
            'password' => bcrypt('0680024530'),
        ]);
        $kycia = User::factory()->create([
            'name' => 'kycia',
            'password' => bcrypt('0680024530'),
        ]);
        Message::factory()->count(3)->create([
            'sender_id' => $kotyk->id,
            'receiver_id' => $kycia->id,
        ]);
        Message::factory()->count(3)->create([
            'sender_id' => $kycia->id,
            'receiver_id' => $kotyk->id,
        ]);
        Message::factory()->count(3)->create([
            'sender_id' => $kotyk->id,
            'receiver_id' => $kycia->id,
        ]);
        Message::factory()->count(3)->create([
            'sender_id' => $kycia->id,
            'receiver_id' => $kotyk->id,
        ]);
    }
}
