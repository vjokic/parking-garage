<?php

namespace App\Listeners;

use App\Events\TicketWasPaid;
use App\Services\TicketService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NextInLineEnters
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  TicketWasPaid  $event
     * @return void
     */
    public function handle(TicketWasPaid $event)
    {
       $event->ticketService->nextInLineEnters();
    }
}
