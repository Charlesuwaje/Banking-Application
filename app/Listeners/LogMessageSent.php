<?php

namespace App\Listeners;

use App\Events\MessageSent;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogMessageSent
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
    public function handle(MessageSent $event)
    {
        $message = $event->message;

        Log::info("Message sent from {$message->user_id} to {$message->receiver_id}: {$message->message}");

    }
}
