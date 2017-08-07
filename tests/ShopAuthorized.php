<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ShopAuthorized extends TestCase
{
    public $order_id;

    public function setUp()
    {
        parent::setUp();
        $this->order_id = 14993121639797;
    }

    public function testAuthorized()
    {
        $response = $this->post('api/shop.authorized/' . $this->shop_id);
        $response->assertStatus(200);
    }
}
