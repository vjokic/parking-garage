<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Ticket;

class Customer extends Model
{
    //

    protected $table = 'customers';

    public function tickets(){

        $tickets = $this->hasMany(Ticket::class);

        return $tickets;
    }

    public function unpaidTickets(){

        return $this->tickets()->unpaid()->get();
    }

    public function hasUnpaidTickets(){

        $count = $this->unpaidTickets()->count();

        if($count == 0){
            return false;
        }else{
            return true;
        }
    }

    public function balance(){

        $tickets = $this->unpaidTickets();

        if(!$tickets){
            return 0;
        }else{

            $total = 0;
            foreach($tickets as $ticket){
                $total += $ticket->cost();
            }

            return $total;
        }
    }

}
