<?php

namespace App\Http\Controllers;

use App\Events\TicketWasPaid;
use Illuminate\Http\Request;
use \App\Ticket;
use \App\Customer;
use Webpatser\Uuid\Uuid;
use \Carbon\Carbon;

use App\Services\TicketService;


class TicketController extends Controller
{
    protected $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $customer_id
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $customer_id)
    {

        $ticket = $this->ticketService->create($customer_id);

        if ($ticket) {
            return response(['ticket' => $ticket], 200);
        }else{
            return response(['errors' => 'Lot is FULL'], 403);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response(['cost' => $this->ticketService->getCost($id)], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        if(! $ticket->cost){
            abort(403, "YOU ARE HERE PREMATURELY");
        }

        // Validate credit card
        $this->validate($request, [
            'credit-card-number' => 'required|ccn',
            'credit-card-date' => 'required|ccd',
            'credit-validation-code' => 'required|cvc',
        ]);

        $message = 'Thank you for your business, your cost is $' . $ticket->cost;

        // Pretend to bill the credit card, assume successful

        $validation_code = Uuid::generate();

        $ticket->is_paid = true;
        $ticket->payment_validation_code = $validation_code;
        $ticket->save();

        // Notify the lot that a ticket was paid and let in first waiting customer (if any)
        event(new TicketWasPaid($this->ticketService));

        return response(['ticket' => $ticket, 'message' => $message], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
