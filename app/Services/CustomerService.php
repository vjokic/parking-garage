<?php
/**
 * Created by PhpStorm.
 * User: vjokic
 * Date: 2017-03-23
 * Time: 11:31 AM
 */

namespace App\Services;

use App\Ticket;
use App\Customer;
use App\Services\TicketService;

class CustomerService
{

    private $customer;

    function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }

    public function getBalance($customer_id){

        $customer = $this->customer->findOrFail($customer_id);

        $tickets = $customer->unpaidTickets();

        if(!$tickets){
            return 0;
        }

        $total = 0;

        $ticketService = new TicketService(new Customer(), new Ticket());
        foreach($tickets as $ticket){
            $total += $ticketService->getCost($ticket->id);
        }

        return $total;
    }
}