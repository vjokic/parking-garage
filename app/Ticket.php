<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class Ticket extends Model
{
    //

	use Uuids;

    public $incrementing = false;

    protected $table = 'tickets';

    public function customer(){

        return $customer = $this->belongsTo(Customer::class);
    }

    public function scopeUnpaid($query) {
        return $query->where('is_paid', false);
    }

    public function cost(){

        $start = $this->created_at;
        $end = Carbon::now();

        $this->updated_at = $end;

        $duration = $end->diffInHours($start);

        $rate = 3;

        if($duration > 1 && $duration <= 3){
            $rate *= 1.5;
        }else if($duration > 3 && $duration <= 6){
            $rate *= 2.25;
        }else if($duration > 6){
            $rate *= 3.375;
        }

        $this->cost = $rate;
        $this->save();

        return $rate;
    }
}
