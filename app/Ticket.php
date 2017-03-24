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

    protected $fillable = ['customer_id'];

    public function customer(){
        return $this->belongsTo(Customer::class);
    }

    public function scopeUnpaid($query) {
        return $query->where('is_paid', false);
    }

    public function hasAvailability(){
        return $this->unpaid()->count() < config('app.garage_capacity');
    }
}
