<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Ticket;
use \App\Customer;
use Webpatser\Uuid\Uuid;
use \Carbon\Carbon;

class TicketController extends Controller
{
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
        $maxCount = 5;

        $ticketCount = Ticket::where('is_paid', 0)->count();

        if ($ticketCount >= $maxCount) {
            abort(403, "LOT IS FULL.");
        }

        $customer = Customer::findOrFail($customer_id);

        if ($customer) {

            $ticket = new Ticket;
            $ticket->customer_id = $customer_id;
            $ticket->save();

            return response($ticket, 200);
        }else{
            abort(404, "Customer with id " . $customer_id . " not found!");
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
        $ticket = Ticket::findOrFail($id);

        if(! $ticket){
            abort(404, "TICKET NOT FOUND! you're stuck, buddy!");
        }

        $ticket->cost();

        return response(['ticket' => $ticket], 200);
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

        if(! $ticket){
            abort(404, "TICKET NOT FOUND! you're stuck, buddy!");
        }else if(! $ticket->cost){
            abort(403, "YOU ARE HERE PREMATURELY");
        }

        // Validate credit card
        $this->validate($request, [
            'credit-card-number' => 'required|ccn',
            'credit-card-date' => 'required|ccd',
            'credit-validation-code' => 'required|cvc',
        ]);

        $message = 'Thank you for your business.';

        // Reclaculate cost after 5 minutes
        $current_time = Carbon::now();
        $timeout = $ticket->updated_at->diffInMinutes($current_time);

        if($timeout >= 5){

            $message = $message . " Updating the cost ... previously it was " . $ticket->cost;

            $ticket->cost();
        }

        $message = $message . ' Your cost is $' . $ticket->cost;

        // Pretend to bill the credit card, assume successful

        $validation_code = Uuid::generate();

        $ticket->is_paid = true;
        $ticket->payment_validation_code = $validation_code;
        $ticket->save();

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
