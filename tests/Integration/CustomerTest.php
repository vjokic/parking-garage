<?php

namespace Tests\Integration;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use \App\Customer;

class CustomerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @group success
     * @return void
     */
    public function testHappyPath()
    {
        // Create some customers
        factory(Customer::class, 5)->create();

        echo ("something");
    }
}
