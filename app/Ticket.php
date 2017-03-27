<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

use App\Events\TicketWasPaid;


class Ticket extends Model
{
    //

	use Uuids;

    public $incrementing = false;

    protected $table = 'tickets';

    protected $fillable = ['customer_id'];

    protected $events = [
        'getsPaid' => TicketWasPaid::class
    ];

    public function customer(){
        return $this->belongsTo(Customer::class);
    }

    public function scopeUnpaid($query) {
        return $query->where('is_paid', false);
    }
}
