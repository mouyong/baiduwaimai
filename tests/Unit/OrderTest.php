<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrderTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testApiOrderHome()
    {
        $response = $this->get('api/order');
        $response->assertStatus(200)
            ->assertExactJson(['errno' => 403, 'error' => 'unauthorized action.']);
    }

    public function testApiOrderCreate()
    {
        $order_id = 14993121639797;
        $shop_id = 1717041709;
        $order = [
            'cmd' => 'order.create',
            'body' => "{\"order_id\":$order_id}",
        ];

        $response = $this->post('api/shop.authorized/' . $shop_id);
        $response->assertStatus(500);
    }
}
