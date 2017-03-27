<?php

namespace Tests\Integration;

use App\Services\CustomerService;
use App\Services\TicketService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use \App\Ticket;
use \App\Customer;

use Mockery as m;

class CustomerTest extends TestCase
{
    use DatabaseTransactions;
    use DatabaseMigrations;

    /**
     * @group success
     * @return void
     */

    public function testLotFullWithMockery(){

        echo (PHP_EOL . "TEST: Lot Full w/Mockery ");
/*
        $ticket = m::mock(Ticket::class);
        $ticket->shouldReceive('hasAvailability')->andReturn(false);

        $ts = new TicketService(new Customer(), $ticket);

        $this->assertEquals(null, $ts->create('123'));
*/
    }

    /**
     * @group lotfull
     * @return void
     */

    public function testWaitingLine()
    {
        // Fill up the lot
        $tickets = factory(Ticket::class, 5)->create();

        // Add new customer
        $customer = factory(Customer::class)->create();

        // Customer attempts to get into lot, gets added to a waiting list
        $response = $this->post('/customers/' . $customer->id);
        $response->assertStatus(403);

        // One customer leaves the lot, waiting customer is added to the lot
        $response = $this->get('/tickets/' . $tickets[0]->id);
        $response->assertStatus(200);

        $response = $this->patch('/pay/' . $tickets[0]->id, ['credit-card-number' => '4520050026416659', 'credit-card-date' => '02/2021', 'credit-validation-code' => '873']);
        $response->assertStatus(200);

        // One customer leaves the lot
        $response = $this->get('/tickets/' . $tickets[1]->id);
        $response->assertStatus(200);

        $response = $this->patch('/pay/' . $tickets[1]->id, ['credit-card-number' => '4520050026416659', 'credit-card-date' => '02/2021', 'credit-validation-code' => '873']);
        $response->assertStatus(200);

        // New customer attempts to get in, gets in fine
        $newCustomer = factory(Customer::class)->create();

        $response = $this->post('/customers/' . $newCustomer->id);
        $response->assertStatus(200);
    }
}
