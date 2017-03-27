<?php
/**
 * Created by PhpStorm.
 * User: vjokic
 * Date: 2017-03-23
 * Time: 11:05 AM
 */

namespace App\Services;

use App\Ticket;
use App\Customer;

use Carbon\Carbon;

class TicketService
{
    /**
     * @var Customer, Ticket
     */
    private $customer, $ticket;

    private $waitingCustomers = array();

    function __construct(Customer $customer, Ticket $ticket)
    {
        $this->customer = $customer;
        $this->ticket = $ticket;
    }

    public function create($customer_id){

        if(!$this->hasAvailability()) {
            // Add customer to waiting list
            array_push($this->waitingCustomers, $customer_id);
            return null;
        }

        $customer = $this->customer->findOrFail($customer_id);

        return $customer->tickets()->create(['customer_id' => $customer_id]);
    }

    public function hasAvailability(){
        return $this->ticket->unpaid()->count() < config('app.garage_capacity');
    }

    public function nextInLineEnters(){

        if(sizeof($this->waitingCustomers) == 0){
            return null;
        }

        $customerId = array_shift($this->waitingCustomers);

        $this->create($customerId);
    }

    public function getCost($ticket_id){

        $ticket = $this->ticket->findOrFail($ticket_id);

        $duration = abs($ticket->created_at->diffInHours(Carbon::now()));

        $cost = 3;

        if($duration > 1 && $duration <= 3){
            $cost *= 1.5;
        }else if($duration > 3 && $duration <= 6){
            $cost *= 2.25;
        }else if($duration > 6){
            $cost *= 3.375;
        }

        $ticket->cost = $cost;
        $ticket->save();

        return $cost;
    }
}