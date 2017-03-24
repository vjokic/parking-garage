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

        $ticket = m::mock(Ticket::class);
        $ticket->shouldReceive('hasAvailability')->andReturn(false);

        $ts = new TicketService(new Customer(), $ticket);

        $this->assertEquals(null, $ts->create('123'));
    }

}
