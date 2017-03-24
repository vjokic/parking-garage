<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Ticket;

class Customer extends Model
{
    //

    protected $table = 'customers';

    public function tickets(){
        return $this->hasMany(Ticket::class);
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
}
