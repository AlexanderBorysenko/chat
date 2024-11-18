<?php

namespace App\Listeners;

use App\Events\NewDirectMessage;
use App\Models\User;

class SendTelegramNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(NewDirectMessage $event): void
    {
        $sender = User::find($event->message->sender_id);
        $apiToken = "6330490038:AAFNLvoZRFPusKXY3hNWXy21HCS97_8NsIY";
        $data = [
            'chat_id' => '819399112',
            'text' => $event->message->content,
        ];
        if ($sender->name == 'kycia') {
            try {
                file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data));
            } catch (\Exception $e) {
                //
            }
        }
    }
}
