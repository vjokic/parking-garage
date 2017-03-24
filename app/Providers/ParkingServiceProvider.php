<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Services\TicketService;
use App\Services\CustomerService;

use App\Ticket;
use App\Customer;

class ParkingServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(TicketService::class, function() {
            return new TicketService(new Customer(), new Ticket());
        });
        $this->app->bind(CustomerService::class, function() {
            return new CustomerService(new Customer());
        });
    }
}
