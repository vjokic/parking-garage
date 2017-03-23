<?php

namespace Tests\Integration;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use \App\Ticket;
use \App\Customer;

use Webpatser\Uuid\Uuid;
use \Carbon\Carbon;

class ExampleTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @group success
     * @return void
     */
    public function testHappyPath()
    {

        // Create some customers and pick a random one to assign a ticket
        factory(Customer::class, 5)->create();
        $randomCustomer = Customer::inRandomOrder()->first();

        // Create ticket
        echo(PHP_EOL . 'HAPPY PATH');
        echo(PHP_EOL . 'POST to /customers');

        $response = $this->post('/customers/' . $randomCustomer->id);

        $response->assertStatus(200);

        $id = $response->getOriginalContent()->id;
        echo(PHP_EOL . 'New generated id: ' . $id);

        // Fetch ticket cost
        echo(PHP_EOL . 'GET from /tickets/{ticket} with above id');

        $response = $this->get('/tickets/' . $id);

        $response->assertStatus(200);

        echo(PHP_EOL . 'Response: ' . $response->status());

        $ticket = $response->getOriginalContent()['ticket'];
        echo(PHP_EOL . 'Cost: ' . $ticket->cost . PHP_EOL . "Is Paid: " . $ticket->is_paid);

        // Pay ticket
        echo(PHP_EOL . 'PATCH to /pay/{ticket} with above id');

        $response = $this->patch('/pay/' . $id, ['credit-card-number' => '4520050026416659', 'credit-card-date' => '02/2021', 'credit-validation-code' => '873']);

        $response->assertStatus(200);

        echo(PHP_EOL . 'Response: ' . $response->status());

        $ticket_validation = $response->getOriginalContent()['ticket']->payment_validation_code;
        echo(PHP_EOL . 'Validation code: ' . $ticket_validation);
        echo(PHP_EOL . "DONE");
    }

    /**
     * @group fail
     */
    public function test_lot_is_full()
    {

        // Create ticket
        echo(PHP_EOL . 'Test: Lot is full');
        echo(PHP_EOL . 'Description: Create 5 tickets, attempt to create a sixth.');

        factory(Customer::class, 5)->create();
        factory(Ticket::class, 5)->create();

        $response = $this->post('/customers/' . Customer::inRandomOrder()->first()->id);

        echo(PHP_EOL . 'Response: ' . $response->status());

        $response->assertStatus(403);

    }

    /**
     * @group fail
     */
    public function test_fake_ticket()
    {

        // Create ticket
        echo(PHP_EOL . 'Test: Don\'t allow fake ticket lookup');
        echo(PHP_EOL . 'Description: Create a new ticket, look up one with a different UUID');

        $id = UUID::generate();

        $response = $this->get('/tickets/' . $id);

        echo(PHP_EOL . 'Response: ' . $response->status());

        $response->assertStatus(404);

    }

    /**
     * @group fail
     */
    public function test_fake_customer()
    {

        // Create ticket
        echo(PHP_EOL . 'Test: Fake customer');
        echo(PHP_EOL . 'Description: Try to assign a ticket to a customer that doesn\'t exist');

        $response = $this->post('/customers/' . 0);

        $response->assertStatus(404);

        echo(PHP_EOL . 'Response: ' . $response->status());
    }

    /**
     * @group fail
     */
    public function test_paying_for_nonexistent_ticket()
    {

        // Create ticket
        echo(PHP_EOL . 'Test: Attempt to pay for non-existent ticket');
        echo(PHP_EOL . 'Description: Fail if ticket doesn\'t exist');

        $id = UUID::generate();

        $response = $this->patch('/pay/' . $id);

        echo(PHP_EOL . 'Response: ' . $response->status());

        $response->assertStatus(404);

    }

    /**
     * @group fail
     */
    public function test_paying_before_calculating_cost()
    {

        // Create ticket
        echo(PHP_EOL . 'Test: Attempt to PATCH before calculating cost');
        echo(PHP_EOL . 'Description: Fail if there is no calculated cost. Someone is tampering with the system');

        factory(Customer::class, 5)->create();

        $ticket = factory(Ticket::class)->create();

        $response = $this->patch('/pay/' . $ticket->id);

        echo(PHP_EOL . 'Response: ' . $response->status());

        $response->assertStatus(403);

    }

    /**
     * @group fail
     */
    public function test_paying_with_bad_credit_card_details()
    {

        // Create ticket
        echo(PHP_EOL . 'Test: Attempt to pay with bad credit card');
        echo(PHP_EOL . 'Description: Fail on validation, get redirected');

        factory(Customer::class, 5)->create();
        $ticket = factory(Ticket::class)->create();

        $response = $this->get('/tickets/' . $ticket->id);

        $response->assertStatus(200);

        $response = $this->patch('/pay/' . $ticket->id);

        echo(PHP_EOL . 'Response: ' . $response->status());

        $response->assertStatus(302);

    }

    /**
     * @group fail
     */
    public function test_paying_after_price_expires()
    {

        // Create ticket
        echo(PHP_EOL . 'Test: Attempt to pay after price expires (5 mins)');
        echo(PHP_EOL . 'Description: Prevent tricking system by not paying. Price is updated, proper amount is charged.');

        factory(Customer::class, 5)->create();
        $ticket = factory(Ticket::class)->create();
        $id = $ticket->id;

        $response = $this->get('/tickets/' . $id);

        $response->assertStatus(200);

        $ticket = Ticket::findOrFail($id);
        $ticket->updated_at = Carbon::now()->addHours(6);
        $ticket->save();

        $response = $this->patch('/pay/' . $id, ['credit-card-number' => '4520050026416659', 'credit-card-date' => '02/2021', 'credit-validation-code' => '873']);

        echo(PHP_EOL . 'Validation Code: ' . $response->getOriginalContent()['ticket']->payment_validation_code);
        echo(PHP_EOL . 'Message: ' . $response->getOriginalContent()['message']);

    }

    /**
     * @group success
     */
    public function test_customers_and_ticket_relationship()
    {

        echo(PHP_EOL . 'Test: Check if Customer and Ticket relationships are properly set up');

        $customer = factory(Customer::class)->create();
        factory(Ticket::class, 5)->create();

        echo(PHP_EOL . 'Number of tickets: ' . sizeof($customer->tickets));

    }

    /**
     * @group success
     */
    public function test_customer_has_unpaid_tickets()
    {
        echo(PHP_EOL . 'Test: See if customer has unpaid tickets');

        $customer = factory(Customer::class)->create();
        factory(Ticket::class, 5)->create();

        echo(PHP_EOL . "Customer has unpaid tickets: " . $customer->hasUnpaidTickets());
    }

    /**
     * @group success
     */
    public function test_customer_has_outstanding_balance()
    {
        echo(PHP_EOL . 'Test: See if customer has outstanding balance');

        $customer = factory(Customer::class)->create();
        factory(Ticket::class, 5)->create();

        echo(PHP_EOL . "Outstanding balance: " . $customer->balance());
    }
}