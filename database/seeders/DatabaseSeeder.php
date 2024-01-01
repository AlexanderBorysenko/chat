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
        $pirozok = User::factory()->create([
            'name' => 'pirozok',
            'password' => bcrypt('pirozok'),
        ]);

        $chat = $kotyk->chats()->create();
        $chat->users()->attach($kycia->id);

        $chat = $kotyk->chats()->create();
        $chat->users()->attach($pirozok->id);

        $chat = $kycia->chats()->create();
        $chat->users()->attach($pirozok->id);
    }
}
